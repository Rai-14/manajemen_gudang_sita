<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">{{ __('Informasi Produk') }}</h3>
                    
                    {{-- Form Create --}}
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Nama Produk --}}
                            <div class="col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Nama Produk</label>
                                <input type="text" name="name" value="{{ old('name') }}" required autofocus class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- SKU --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">SKU (Kode Unik)</label>
                                <input type="text" name="sku" value="{{ old('sku') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('sku') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kategori --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Kategori</label>
                                <select name="category_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Harga Beli --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Harga Beli (Rp)</label>
                                <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" required min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('purchase_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Harga Jual --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Harga Jual (Rp)</label>
                                <input type="number" name="selling_price" value="{{ old('selling_price') }}" required min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('selling_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Stok Saat Ini --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Stok Awal</label>
                                <input type="number" name="current_stock" value="{{ old('current_stock', 0) }}" required min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('current_stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Stok Minimum --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Stok Minimum (Alert)</label>
                                <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" required min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('min_stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Satuan & Lokasi --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Satuan (Unit)</label>
                                <input type="text" name="unit" value="{{ old('unit', 'Pcs') }}" required placeholder="Pcs, Box, Kg..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Lokasi Rak</label>
                                <input type="text" name="rack_location" value="{{ old('rack_location') }}" placeholder="Contoh: A-01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Deskripsi --}}
                            <div class="col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Deskripsi</label>
                                <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                            </div>

                            {{-- Gambar --}}
                            <div class="col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Gambar Produk</label>
                                <input type="file" name="image_path" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                @error('image_path') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6 border-t pt-4">
                            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4 font-medium">Batal</a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow-lg">Simpan Produk</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>