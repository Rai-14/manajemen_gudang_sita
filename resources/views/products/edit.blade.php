<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Produk') }}: {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Perbarui Informasi Produk') }}</h3>

                    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Kiri: Informasi Dasar --}}
                            <div>
                                {{-- Nama Produk --}}
                                <div class="mb-4">
                                    <label for="name" class="block font-medium text-sm text-gray-700">Nama Produk</label>
                                    <input id="name" type="text" name="name" 
                                        value="{{ old('name', $product->name) }}" required autofocus
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- SKU (Disabled) --}}
                                <div class="mb-4">
                                    <label for="sku" class="block font-medium text-sm text-gray-700">SKU (Tidak dapat diubah)</label>
                                    <input id="sku" type="text" name="sku" value="{{ $product->sku }}" disabled
                                        class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm cursor-not-allowed">
                                    {{-- Kirim SKU hidden agar validasi unique (ignore ID) tetap berjalan lancar --}}
                                    <input type="hidden" name="sku" value="{{ $product->sku }}">
                                </div>

                                {{-- Kategori --}}
                                <div class="mb-4">
                                    <label for="category_id" class="block font-medium text-sm text-gray-700">Kategori</label>
                                    <select id="category_id" name="category_id" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('category_id') border-red-500 @enderror">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                {{-- Unit --}}
                                <div class="mb-4">
                                    <label for="unit" class="block font-medium text-sm text-gray-700">Satuan (Pcs, Box, Kg, dll)</label>
                                    <input id="unit" type="text" name="unit" value="{{ old('unit', $product->unit) }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('unit') border-red-500 @enderror">
                                    @error('unit')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Kanan: Harga, Stok, Lokasi --}}
                            <div>
                                {{-- Harga Beli --}}
                                <div class="mb-4">
                                    <label for="purchase_price" class="block font-medium text-sm text-gray-700">Harga Beli (Rp)</label>
                                    <input id="purchase_price" type="number" step="0.01" name="purchase_price" 
                                        value="{{ old('purchase_price', $product->purchase_price) }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('purchase_price') border-red-500 @enderror">
                                    @error('purchase_price')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Harga Jual --}}
                                <div class="mb-4">
                                    <label for="selling_price" class="block font-medium text-sm text-gray-700">Harga Jual (Rp)</label>
                                    <input id="selling_price" type="number" step="0.01" name="selling_price" 
                                        value="{{ old('selling_price', $product->selling_price) }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('selling_price') border-red-500 @enderror">
                                    @error('selling_price')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Stok Saat Ini --}}
                                <div class="mb-4">
                                    <label for="current_stock" class="block font-medium text-sm text-gray-700">Stok Saat Ini</label>
                                    <input id="current_stock" type="number" name="current_stock" 
                                        value="{{ old('current_stock', $product->current_stock) }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('current_stock') border-red-500 @enderror">
                                    @error('current_stock')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Stok Minimum --}}
                                <div class="mb-4">
                                    <label for="min_stock" class="block font-medium text-sm text-gray-700">Minimum Stok (Alert)</label>
                                    <input id="min_stock" type="number" name="min_stock" 
                                        value="{{ old('min_stock', $product->min_stock) }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('min_stock') border-red-500 @enderror">
                                    @error('min_stock')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Lokasi Rak --}}
                                <div class="mb-4">
                                    <label for="rack_location" class="block font-medium text-sm text-gray-700">Lokasi Rak</label>
                                    <input id="rack_location" type="text" name="rack_location" 
                                        value="{{ old('rack_location', $product->rack_location) }}"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('rack_location') border-red-500 @enderror">
                                    @error('rack_location')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        {{-- Deskripsi --}}
                        <div class="mb-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi</label>
                            <textarea id="description" name="description" rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Gambar Produk --}}
                        <div class="mb-6">
                            <label for="image_path" class="block font-medium text-sm text-gray-700">Gambar Produk (Biarkan kosong jika tidak ingin mengubah)</label>
                            <input id="image_path" type="file" name="image_path"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            
                            @if ($product->image_path)
                                <div class="mt-2 flex items-center">
                                    <span class="text-xs text-gray-500 mr-2">Gambar Saat Ini:</span>
                                    <a href="{{ Storage::url($product->image_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-xs underline">Lihat Gambar</a>
                                </div>
                            @endif

                            @error('image_path')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end border-t pt-4">
                            <a href="{{ route('products.index') }}" class="mr-4 text-sm font-semibold text-gray-600 hover:text-gray-900">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Simpan Perubahan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>