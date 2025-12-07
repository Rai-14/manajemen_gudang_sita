<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Produk & Stok') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Header & Tombol Tambah --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-bold text-gray-900">Inventaris Gudang</h3>
                        
                        {{-- Hanya Admin & Manager yang bisa tambah produk --}}
                        @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Produk
                            </a>
                        @endif
                    </div>

                    {{-- Form Filter & Search --}}
                    <form method="GET" action="{{ route('products.index') }}" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            {{-- Search --}}
                            <div class="col-span-1 md:col-span-1">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Produk / SKU</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" placeholder="Nama atau SKU...">
                            </div>

                            {{-- Filter Kategori --}}
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select name="category_id" id="category_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Status Stok --}}
                            <div>
                                <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-1">Status Stok</label>
                                <select name="stock_status" id="stock_status" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <option value="">Semua Status</option>
                                    <option value="available" @selected(request('stock_status') == 'available')>✅ Stok Aman</option>
                                    <option value="low_stock" @selected(request('stock_status') == 'low_stock')>⚠️ Stok Menipis (Low)</option>
                                    <option value="out_of_stock" @selected(request('stock_status') == 'out_of_stock')>❌ Stok Habis</option>
                                </select>
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="flex items-end gap-2">
                                <button type="submit" class="w-full bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 px-4 py-2">
                                    Filter
                                </button>
                                <a href="{{ route('products.index') }}" class="w-auto bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 px-4 py-2 text-center">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Tabel Produk --}}
                    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($products as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($product->image_path)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $product->image_path) }}" alt="">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold">
                                                            N/A
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                    <div class="text-xs text-gray-500">SKU: {{ $product->sku }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $product->category->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($product->current_stock == 0)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Habis</span>
                                            @elseif($product->isLowStock())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Low: {{ $product->current_stock }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $product->current_stock }} {{ $product->unit }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $product->rack_location ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900 mr-3 font-bold">Detail</a>
                                            
                                            @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 font-bold">Edit</a>
                                                
                                                {{-- TOMBOL HAPUS (DITAMBAHKAN) --}}
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold bg-transparent border-none cursor-pointer">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                <p>Tidak ada produk ditemukan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>