@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Product Management') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Notifikasi Status --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ __('List Products') }}
                        </h3>
                        {{-- Cek Role untuk tombol Add --}}
                        @if (Auth::user()->isAdmin() || Auth::user()->isManager())
                            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                {{ __('Add New Product') }}
                            </a>
                        @endif
                    </div>

                    {{-- Form Pencarian, Filter & Sorting --}}
                    <form method="GET" action="{{ route('products.index') }}" class="mb-6">
                        <div class="flex flex-wrap items-end space-x-0 sm:space-x-4 space-y-4 sm:space-y-0">
                            {{-- Input Search --}}
                            <div class="w-full sm:w-1/4">
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <input type="text" name="search" id="search" placeholder="Name or SKU..." value="{{ request('search') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            {{-- Filter Kategori --}}
                            <div class="w-full sm:w-1/4">
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Filter Category</label>
                                <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All Categories</option>
                                    {{-- Loop Data Kategori dari Controller --}}
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Status Stok --}}
                            <div class="w-full sm:w-1/4">
                                <label for="stock_status" class="block text-sm font-medium text-gray-700">Filter Stock</label>
                                <select name="stock_status" id="stock_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All Status</option>
                                    <option value="available" @selected(request('stock_status') == 'available')>Available (Stok Aman)</option>
                                    <option value="low_stock" @selected(request('stock_status') == 'low_stock')>Low Stock (Hampir Habis)</option>
                                    <option value="out_of_stock" @selected(request('stock_status') == 'out_of_stock')>Out of Stock (Habis)</option>
                                </select>
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="flex space-x-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Filter') }}
                                </button>
                                {{-- Tombol Reset Filter --}}
                                @if (request()->hasAny(['search', 'category_id', 'stock_status', 'sort_by', 'sort_direction']))
                                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Reset') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Sorting (Hidden fields for sorting logic) --}}
                        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'name') }}">
                        <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'asc') }}">
                    </form>

                    {{-- Tabel Produk --}}
                    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    {{-- Kolom SKU --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('SKU') }}
                                    </th>
                                    {{-- Kolom Product Name dengan Sorting --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="window.location='{{ route('products.index', array_merge(request()->except(['sort_by', 'sort_direction']), ['sort_by' => 'name', 'sort_direction' => request('sort_by') == 'name' && request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                        {{ __('Product Name') }}
                                        @if (request('sort_by') == 'name')
                                            {!! request('sort_direction') == 'asc' ? '&#9650;' : '&#9660;' !!}
                                        @endif
                                    </th>
                                    {{-- Kolom Category --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Category') }}
                                    </th>
                                    {{-- Kolom Stock dengan Sorting --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="window.location='{{ route('products.index', array_merge(request()->except(['sort_by', 'sort_direction']), ['sort_by' => 'current_stock', 'sort_direction' => request('sort_by') == 'current_stock' && request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                        {{ __('Stock') }}
                                        @if (request('sort_by') == 'current_stock')
                                            {!! request('sort_direction') == 'asc' ? '&#9650;' : '&#9660;' !!}
                                        @endif
                                    </th>
                                    {{-- Kolom Location --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Location') }}
                                    </th>
                                    {{-- Kolom Action --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->sku }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                                        {{-- Menggunakan relasi category() --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{-- Cek Low Stock menggunakan helper isLowStock() --}}
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if ($product->isLowStock())
                                                    bg-red-100 text-red-800 {{-- Low Stock / Habis --}}
                                                @else
                                                    bg-green-100 text-green-800 {{-- Stock Aman --}}
                                                @endif
                                            ">
                                                {{ $product->current_stock }} {{ $product->unit }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->rack_location ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('View') }}</a>
                                            {{-- Cek Role untuk tombol Edit/Delete --}}
                                            @if (Auth::user()->isAdmin() || Auth::user()->isManager())
                                                <a href="{{ route('products.edit', $product) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">{{ __('Edit') }}</a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $product->name }}? Pastikan stoknya 0.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Delete') }}</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('No products found.') }}
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
@endsection