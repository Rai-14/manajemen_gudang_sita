@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Product Detail') }}: {{ $product->name }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Header dan Tombol Aksi --}}
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-900">{{ $product->name }}</h3>
                        <div>
                            @if (Auth::user()->isAdmin() || Auth::user()->isManager())
                                {{-- Tombol Edit --}}
                                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                    {{ __('Edit Product') }}
                                </a>
                                
                                {{-- Tombol Delete (Perlu Konfirmasi) --}}
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $product->name }}? Stok: {{ $product->current_stock }}.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Detail Produk --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        {{-- Kolom Kiri: Gambar dan Deskripsi --}}
                        <div class="lg:col-span-1">
                            @if ($product->image_path)
                                {{-- Menampilkan Gambar (Pastikan Anda sudah menjalankan php artisan storage:link) --}}
                                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-auto rounded-lg shadow-lg object-cover mb-4">
                            @else
                                <div class="bg-gray-200 h-48 w-full rounded-lg flex items-center justify-center text-gray-500 mb-4">
                                    No Image Available
                                </div>
                            @endif
                            
                            <h4 class="font-semibold text-gray-700 mb-2">{{ __('Description') }}</h4>
                            <p class="text-gray-600 text-sm">{{ $product->description ?? 'No description provided.' }}</p>
                        </div>

                        {{-- Kolom Tengah: Info Utama --}}
                        <div class="lg:col-span-2 space-y-4">
                            
                            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-lg text-indigo-700 mb-3">{{ __('General Information') }}</h4>
                                <dl class="divide-y divide-gray-200">
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">SKU:</dt>
                                        <dd class="text-sm text-gray-900 font-mono">{{ $product->sku }}</dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Category:</dt>
                                        <dd class="text-sm text-gray-900">{{ $product->category->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Unit:</dt>
                                        <dd class="text-sm text-gray-900">{{ $product->unit }}</dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Rack Location:</dt>
                                        <dd class="text-sm text-gray-900">{{ $product->rack_location ?? '-' }}</dd>
                                    </div>
                                </dl>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-lg text-indigo-700 mb-3">{{ __('Stock & Pricing') }}</h4>
                                <dl class="divide-y divide-gray-200">
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Current Stock:</dt>
                                        <dd class="text-sm text-gray-900 font-bold">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if ($product->isLowStock()) 
                                                    bg-red-100 text-red-800 
                                                @else 
                                                    bg-green-100 text-green-800 
                                                @endif">
                                                {{ $product->current_stock }} {{ $product->unit }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Minimum Stock Alert:</dt>
                                        <dd class="text-sm text-gray-900">{{ $product->min_stock }} {{ $product->unit }}</dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Purchase Price (Cost):</dt>
                                        <dd class="text-sm text-gray-900">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Selling Price:</dt>
                                        <dd class="text-sm text-gray-900 font-bold">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</dd>
                                    </div>
                                    @if (Auth::user()->isManager() && $product->isLowStock())
                                        <div class="py-2 flex justify-end">
                                            {{-- Tombol untuk Manager jika stok rendah --}}
                                            <a href="#" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                {{ __('Create Restock Order') }}
                                            </a>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            {{-- Riwayat Transaksi (Placeholder) --}}
                            <div class="mt-8">
                                <h4 class="font-semibold text-lg text-gray-900 mb-3">{{ __('Last 5 Transactions') }}</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            {{-- Data ini akan diisi di tahap Transaction Management --}}
                                            <tr>
                                                <td colspan="4" class="px-3 py-2 text-sm text-gray-500 text-center italic">
                                                    Riwayat transaksi akan muncul di sini setelah modul Transaksi diimplementasikan.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection