@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Create New Product') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Product Information') }}</h3>

                    {{-- Form menuju ProductController@store --}}
                    {{-- Note: enctype="multipart/form-data" diperlukan karena ada input file (Gambar produk) --}}
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Kiri: Informasi Dasar --}}
                            <div>
                                {{-- Nama Produk --}}
                                <div class="mb-4">
                                    <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- SKU --}}
                                <div class="mb-4">
                                    <label for="sku" class="block font-medium text-sm text-gray-700">SKU (Stock Keeping Unit)</label>
                                    <input id="sku" type="text" name="sku" value="{{ old('sku') }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('sku') border-red-500 @enderror">
                                    @error('sku')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Kategori --}}
                                <div class="mb-4">
                                    <label for="category_id" class="block font-medium text-sm text-gray-700">Category</label>
                                    <select id="category_id" name="category_id" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('category_id') border-red-500 @enderror">
                                        <option value="">-- Select Category --</option>
                                        {{-- $categories dikirim dari ProductController@create --}}
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                    <label for="unit" class="block font-medium text-sm text-gray-700">Unit (pcs, box, kg, etc.)</label>
                                    <input id="unit" type="text" name="unit" value="{{ old('unit', 'pcs') }}" required
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
                                    <label for="purchase_price" class="block font-medium text-sm text-gray-700">Purchase Price (Rp)</label>
                                    <input id="purchase_price" type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('purchase_price') border-red-500 @enderror">
                                    @error('purchase_price')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Harga Jual --}}
                                <div class="mb-4">
                                    <label for="selling_price" class="block font-medium text-sm text-gray-700">Selling Price (Rp)</label>
                                    <input id="selling_price" type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('selling_price') border-red-500 @enderror">
                                    @error('selling_price')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Stok Saat Ini --}}
                                <div class="mb-4">
                                    <label for="current_stock" class="block font-medium text-sm text-gray-700">Current Stock</label>
                                    <input id="current_stock" type="number" name="current_stock" value="{{ old('current_stock', 0) }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('current_stock') border-red-500 @enderror">
                                    @error('current_stock')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Stok Minimum --}}
                                <div class="mb-4">
                                    <label for="min_stock" class="block font-medium text-sm text-gray-700">Minimum Stock (for Alert)</label>
                                    <input id="min_stock" type="number" name="min_stock" value="{{ old('min_stock', 10) }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('min_stock') border-red-500 @enderror">
                                    @error('min_stock')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Lokasi Rak --}}
                                <div class="mb-4">
                                    <label for="rack_location" class="block font-medium text-sm text-gray-700">Rack Location</label>
                                    <input id="rack_location" type="text" name="rack_location" value="{{ old('rack_location') }}"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('rack_location') border-red-500 @enderror">
                                    @error('rack_location')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        {{-- Deskripsi (Ambil full width) --}}
                        <div class="mb-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Gambar Produk (Optional) --}}
                        <div class="mb-6">
                            <label for="image_path" class="block font-medium text-sm text-gray-700">Product Image (Optional)</label>
                            <input id="image_path" type="file" name="image_path"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('image_path')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end">
                            <a href="{{ route('products.index') }}" class="mr-4 text-sm font-semibold text-gray-600 hover:text-gray-900">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Save Product') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection