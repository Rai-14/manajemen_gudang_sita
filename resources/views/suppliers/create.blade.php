<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Supplier Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Perusahaan (PT/CV)</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: PT. Elektronik Jaya">
                    </div>

                    <!-- TAMBAHAN BARU: INPUT EMAIL -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email Perusahaan</label>
                        <input type="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="email@perusahaan.com">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Telepon</label>
                        <input type="text" name="phone" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="0812...">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Lengkap</label>
                        <textarea name="address" class="w-full border-gray-300 rounded-md shadow-sm" rows="3"></textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('suppliers.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Supplier</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>