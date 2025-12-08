<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        // PERBAIKAN: Menambahkan validasi 'email'
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
            'email' => 'required|email|max:255|unique:suppliers,email', // <--- INI PENTING
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'PT/Supplier baru berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        // PERBAIKAN: Menambahkan validasi 'email' (abaikan ID sendiri saat update)
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,'.$supplier->id,
            'email' => 'required|email|max:255|unique:suppliers,email,'.$supplier->id, // <--- INI PENTING
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Data Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: Supplier ini masih memiliki data pesanan terkait.');
        }
    }
}