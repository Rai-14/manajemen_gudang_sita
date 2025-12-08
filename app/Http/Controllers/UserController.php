<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Urutkan status 'pending' paling atas agar Admin langsung lihat
        $users = User::with('supplier')
                    ->orderByRaw("FIELD(status, 'pending', 'active')")
                    ->latest()
                    ->paginate(10);
                    
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('users.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,manager,staff,supplier'],
            'supplier_id' => ['nullable', 'required_if:role,supplier', 'exists:suppliers,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'supplier_id' => $request->role === 'supplier' ? $request->supplier_id : null,
            'status' => 'active', // Jika Admin yang buat manual, langsung aktif
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $suppliers = Supplier::all();
        return view('users.edit', compact('user', 'suppliers'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,manager,staff,supplier'],
            'supplier_id' => ['nullable', 'required_if:role,supplier', 'exists:suppliers,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->supplier_id = $request->role === 'supplier' ? $request->supplier_id : null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return back()->with('error', 'Gagal menghapus: Pengguna ini memiliki riwayat transaksi.');
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menyetujui user pending
     */
    public function approve(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', "Akun {$user->name} berhasil disetujui dan diaktifkan.");
    }
}