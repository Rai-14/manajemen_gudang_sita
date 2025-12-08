<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pengguna Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <!-- Nama -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full border rounded px-3 py-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" required placeholder="Nama User">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email Login</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full border rounded px-3 py-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" required placeholder="email@contoh.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Peran (Role)</label>
                        <select name="role" id="roleSelect" class="w-full border rounded px-3 py-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="toggleSupplierInput()">
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff Gudang</option>
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Warehouse Manager</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="supplier" {{ old('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Supplier Dropdown (Hidden by default unless Supplier role selected) -->
                    <div class="mb-4 {{ old('role') == 'supplier' ? '' : 'hidden' }}" id="supplierInputDiv">
                        <label class="block text-gray-700 text-sm font-bold mb-2 text-blue-600">
                            Pilih Perusahaan Supplier <span class="text-red-500">*</span>
                        </label>
                        <select name="supplier_id" class="w-full border rounded px-3 py-2 border-blue-300 bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('supplier_id') border-red-500 @enderror">
                            <option value="">-- Pilih PT/CV --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">*Wajib dipilih jika Role adalah Supplier. Akun ini akan terhubung ke data PT tersebut.</p>
                        @error('supplier_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                            <input type="password" name="password" 
                                   class="w-full border rounded px-3 py-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror" required>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition shadow-lg">Simpan Pengguna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSupplierInput() {
            const role = document.getElementById('roleSelect').value;
            const supplierDiv = document.getElementById('supplierInputDiv');
            
            if (role === 'supplier') {
                supplierDiv.classList.remove('hidden');
            } else {
                supplierDiv.classList.add('hidden');
                // Optional: Reset selection if hidden
                // supplierDiv.querySelector('select').value = ""; 
            }
        }
    </script>
</x-app-layout>