<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Buat Pengguna Baru</h2>

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <!-- Nama -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                            <input type="password" name="password" 
                                   class="w-full border rounded px-3 py-2 @error('password') border-red-500 @enderror" required>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Peran (Role)</label>
                        <select name="role" id="roleSelect" class="w-full border rounded px-3 py-2" onchange="toggleSupplierInput()">
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff Gudang</option>
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Warehouse Manager</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="supplier" {{ old('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Supplier Dropdown -->
                    <div class="mb-6 {{ old('role') == 'supplier' ? '' : 'hidden' }}" id="supplierInputDiv">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Perusahaan Supplier</label>
                        <select name="supplier_id" class="w-full border rounded px-3 py-2 @error('supplier_id') border-red-500 @enderror">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">*Pastikan data Supplier sudah dibuat di menu Master Data</p>
                        @error('supplier_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan User</button>
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
            }
        }
    </script>
</x-app-layout>