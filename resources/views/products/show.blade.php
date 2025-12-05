<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Produk') }}: {{ $product->name }}
        </h2>
    </x-slot>

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
                                    {{ __('Edit Produk') }}
                                </a>
                                
                                {{-- Tombol Delete (Perlu Konfirmasi) --}}
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $product->name }}? Stok: {{ $product->current_stock }}.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Hapus') }}
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
                                {{-- Menampilkan Gambar --}}
                                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-auto rounded-lg shadow-lg object-cover mb-4">
                            @else
                                <div class="bg-gray-200 h-48 w-full rounded-lg flex items-center justify-center text-gray-500 mb-4">
                                    Tidak Ada Gambar
                                </div>
                            @endif
                            
                            <h4 class="font-semibold text-gray-700 mb-2">{{ __('Deskripsi') }}</h4>
                            <div class="text-gray-600 text-sm bg-gray-50 p-3 rounded border">
                                {{ $product->description ?? 'Tidak ada deskripsi.' }}
                            </div>
                        </div>

                        {{-- Kolom Tengah: Info Utama --}}
                        <div class="lg:col-span-2 space-y-4">
                            
                            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-lg text-indigo-700 mb-3">{{ __('Informasi Umum') }}</h4>
                                <dl class="divide-y divide-gray-200">
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">SKU:</dt>
                                        <dd class="text-sm text-gray-900 font-mono font-bold">{{ $product->sku }}</dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Kategori:</dt>
                                        <dd class="text-sm text-gray-900">{{ $product->category->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Satuan (Unit):</dt>
                                        <dd class="text-sm text-gray-900">{{ $product->unit }}</dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Lokasi Rak:</dt>
                                        <dd class="text-sm text-gray-900">{{ $product->rack_location ?? '-' }}</dd>
                                    </div>
                                </dl>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-lg text-indigo-700 mb-3">{{ __('Stok & Harga') }}</h4>
                                <dl class="divide-y divide-gray-200">
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Stok Saat Ini:</dt>
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
                                        <dt class="text-sm font-medium text-gray-500">Alert Minimum Stok:</dt>
                                        <dd class="text-sm text-gray-900">{{ $product->min_stock }} {{ $product->unit }}</dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Harga Beli (Modal):</dt>
                                        <dd class="text-sm text-gray-900">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</dd>
                                    </div>
                                    <div class="py-2 flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Harga Jual:</dt>
                                        <dd class="text-sm text-gray-900 font-bold text-green-600">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</dd>
                                    </div>
                                    
                                    {{-- Shortcut Restock untuk Manager --}}
                                    @if (Auth::user()->isManager() && $product->isLowStock())
                                        <div class="py-3 flex justify-end">
                                            <a href="{{ route('restock_orders.create') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 shadow-sm">
                                                {{ __('Buat Restock Order') }}
                                            </a>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            {{-- Riwayat Transaksi (Placeholder) --}}
                            <div class="mt-8">
                                <h4 class="font-semibold text-lg text-gray-900 mb-3">{{ __('Riwayat Transaksi Terakhir') }}</h4>
                                <div class="overflow-x-auto border rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            {{-- Nanti bisa diisi dengan relasi $product->transactionDetails --}}
                                            <tr>
                                                <td colspan="3" class="px-3 py-4 text-sm text-gray-500 text-center italic">
                                                    Fitur riwayat per produk akan aktif setelah data transaksi tersedia.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    {{-- Tombol Kembali --}}
                    <div class="mt-6">
                        <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                            &larr; Kembali ke Daftar Produk
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>