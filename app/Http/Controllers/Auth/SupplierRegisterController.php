<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SupplierRegisterController extends Controller
{
    public function create()
    {
        return view('auth.register-supplier');
    }

    public function store(Request $request)
    {
        $request->validate([
            // DATA PERUSAHAAN (Hapus validasi company_email)
            'company_name' => 'required|string|max:255|unique:suppliers,name',
            'company_phone' => 'required|string|max:20',
            'company_address' => 'required|string',
            
            // DATA LOGIN
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat Data PT Supplier
            $supplier = Supplier::create([
                'name' => $request->company_name,
                'email' => $request->email, // <--- PAKAI EMAIL LOGIN (Otomatis)
                'phone' => $request->company_phone,
                'address' => $request->company_address,
            ]);

            // 2. Buat Akun User (Status: Pending)
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'supplier',
                'supplier_id' => $supplier->id,
                'status' => 'pending', 
            ]);

            DB::commit();

            return redirect()->route('portal.supplier')->with('success', 'Pendaftaran Berhasil! Akun Anda sedang menunggu persetujuan Admin.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}