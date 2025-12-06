<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier; // Pastikan model Supplier ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar pengguna.
     */
    public function index()
    {
        // Ambil data user, urutkan terbaru, dan paginate 10 per halaman
        $users = User::with('supplier')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Tampilkan form tambah pengguna baru.
     */
    public function create()
    {
        // Kita butuh data supplier untuk dropdown jika admin ingin bikin akun supplier
        // Pastikan Model Supplier sudah ada. Jika belum, hapus baris ini.
        $suppliers = \App\Models\Supplier::all(); 
        
        return view('users.create', compact('suppliers'));
    }

    /**
     * Simpan data pengguna baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'], // butuh input password_confirmation di form
            'role' => ['required', 'in:admin,manager,staff,supplier'],
            // supplier_id wajib diisi HANYA JIKA role = supplier
            'supplier_id' => ['nullable', 'required_if:role,supplier', 'exists:suppliers,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'supplier_id' => $request->role === 'supplier' ? $request->supplier_id : null,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit pengguna.
     */
    public function edit(User $user)
    {
        $suppliers = \App\Models\Supplier::all();
        return view('users.edit', compact('user', 'suppliers'));
    }

    /**
     * Update data pengguna.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,manager,staff,supplier'],
            'supplier_id' => ['nullable', 'required_if:role,supplier', 'exists:suppliers,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Password opsional saat edit
        ]);

        // Update data dasar
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->supplier_id = $request->role === 'supplier' ? $request->supplier_id : null;

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    /**
     * Hapus pengguna.
     */
    public function destroy(User $user)
    {
        // Mencegah admin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}