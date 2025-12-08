<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Perusahaan (PT/CV)</label>
                        <input type="text" name="name" value="{{ $supplier->name }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- TAMBAHAN BARU: INPUT EMAIL -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email Perusahaan</label>
                        <input type="email" name="email" value="{{ $supplier->email }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ $supplier->phone }}" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Lengkap</label>
                        <textarea name="address" class="w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ $supplier->address }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('suppliers.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Supplier</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>