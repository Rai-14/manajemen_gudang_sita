<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Edit Pengguna: {{ $user->name }}</h2>

                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Nama -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                               class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                               class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror" required>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Peran (Role)</label>
                        <select name="role" id="roleSelect" class="w-full border rounded px-3 py-2" onchange="toggleSupplierInput()">
                            <option value="staff" {{ (old('role') ?? $user->role) == 'staff' ? 'selected' : '' }}>Staff Gudang</option>
                            <option value="manager" {{ (old('role') ?? $user->role) == 'manager' ? 'selected' : '' }}>Warehouse Manager</option>
                            <option value="admin" {{ (old('role') ?? $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="supplier" {{ (old('role') ?? $user->role) == 'supplier' ? 'selected' : '' }}>Supplier</option>
                        </select>
                    </div>

                    <!-- Supplier Dropdown (TAMBAHAN BARU) -->
                    <!-- Logic: Tampilkan jika role user saat ini adalah supplier, atau jika old input role adalah supplier -->
                    <div class="mb-4 {{ (old('role') ?? $user->role) == 'supplier' ? '' : 'hidden' }}" id="supplierInputDiv">
                        <label class="block text-gray-700 text-sm font-bold mb-2 text-blue-600">Pilih Perusahaan Supplier</label>
                        <select name="supplier_id" class="w-full border rounded px-3 py-2 border-blue-300 bg-blue-50 @error('supplier_id') border-red-500 @enderror">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ (old('supplier_id') ?? $user->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">*Akun ini terhubung ke data PT Supplier tersebut.</p>
                        @error('supplier_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password Change (Optional) -->
                    <div class="mt-6 pt-6 border-t">
                        <h3 class="text-sm font-bold text-gray-500 mb-2">Ganti Password (Opsional)</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="password" name="password" placeholder="Password Baru" 
                                       class="w-full border rounded px-3 py-2 @error('password') border-red-500 @enderror">
                                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <input type="password" name="password_confirmation" placeholder="Ulangi Password" class="w-full border rounded px-3 py-2">
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update User</button>
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