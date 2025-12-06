<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Card 1 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium">Total Produk</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $totalProducts }} <span class="text-sm font-normal">Item</span></div>
                </div>
                <!-- Card 2 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium">Total Stok Unit</div>
                    <div class="text-3xl font-bold text-gray-800">{{ number_format($totalStock) }} <span class="text-sm font-normal">Unit</span></div>
                </div>
                <!-- Card 3 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm font-medium">Butuh Approval</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $pendingApprovals }} <span class="text-sm font-normal">Transaksi</span></div>
                    @if($pendingApprovals > 0)
                        <a href="{{ route('transactions.index') }}" class="text-xs text-blue-600 hover:underline mt-1 block">Lihat Transaksi &rarr;</a>
                    @endif
                </div>
            </div>

            <!-- LOW STOCK ALERT (Sesuai Ekspektasi) -->
            @if($lowStockProducts->count() > 0)
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold text-red-700 flex items-center mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Peringatan Stok Minimum
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-red-100 text-red-800 uppercase font-bold">
                            <tr>
                                <th class="px-4 py-2">Nama Produk</th>
                                <th class="px-4 py-2">Kategori</th>
                                <th class="px-4 py-2">Sisa Stok</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-200">
                            @foreach($lowStockProducts as $product)
                            <tr class="hover:bg-red-100/50">
                                <td class="px-4 py-2 font-medium">{{ $product->name }}</td>
                                <td class="px-4 py-2">{{ $product->category->name ?? '-' }}</td>
                                <td class="px-4 py-2 font-bold text-red-600">{{ $product->stock }} Unit</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('restock_orders.create') }}?product_id={{ $product->id }}" class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                                        Restock Sekarang
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>