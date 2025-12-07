<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Portal Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- 1. SECTION: PESANAN MASUK (Action Needed) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-400">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-bell text-yellow-500 mr-2"></i>Pesanan Masuk (Perlu Konfirmasi)
                        </h3>
                        @if(!$active_orders->isEmpty())
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                {{ $active_orders->count() }} Menunggu
                            </span>
                        @endif
                    </div>
                    
                    @if($active_orders->isEmpty())
                        <div class="text-center py-6 text-gray-500 bg-gray-50 rounded-lg">
                            <p>Tidak ada pesanan baru yang perlu diproses.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="p-3 text-sm font-semibold text-gray-600 w-32">Tanggal</th>
                                        <th class="p-3 text-sm font-semibold text-gray-600 w-32">ID Order</th>
                                        <th class="p-3 text-sm font-semibold text-gray-600">Detail Barang</th>
                                        <th class="p-3 text-sm font-semibold text-gray-600 text-center w-24">Total Qty</th>
                                        <th class="p-3 text-sm font-semibold text-gray-600 w-40">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($active_orders as $order)
                                    <tr class="hover:bg-gray-50 align-top">
                                        <td class="p-3 text-sm text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="p-3 text-sm font-mono text-blue-600 font-bold">#{{ $order->id }}</td>
                                        
                                        <!-- Detail Barang (Nama) -->
                                        <td class="p-3 text-sm">
                                            @if($order->details->count() > 0)
                                                <div class="space-y-1">
                                                    @foreach($order->details as $detail)
                                                        <div class="text-gray-700 font-medium border-b border-gray-100 last:border-0 pb-1">
                                                            {{ $detail->product->name ?? 'Produk dihapus' }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">Tidak ada item</span>
                                            @endif
                                        </td>

                                        <!-- Detail Jumlah (Qty) -->
                                        <td class="p-3 text-sm text-center">
                                            @if($order->details->count() > 0)
                                                <div class="space-y-1">
                                                    @foreach($order->details as $detail)
                                                        <div class="text-gray-800 font-bold bg-gray-100 rounded px-2 py-0.5 inline-block text-xs">
                                                            {{ $detail->quantity }} {{ $detail->product->unit ?? 'pcs' }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        
                                        <td class="p-3">
                                            <form action="{{ route('restock_orders.update_status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="In Transit">
                                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded text-xs font-semibold hover:bg-blue-700 transition shadow-sm flex items-center justify-center gap-2">
                                                    <i class="fas fa-truck"></i> Kirim Barang
                                                </button>
                                            </form>
                                            <div class="mt-2 text-center">
                                                <a href="{{ route('restock_orders.show', $order->id) }}" class="text-xs text-blue-500 hover:underline">Lihat Detail</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. SECTION: RIWAYAT PENGIRIMAN (STATUS: IN TRANSIT) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-shipping-fast text-blue-500 mr-2"></i>Dalam Pengiriman (On Delivery)
                    </h3>

                    @if($shipped_orders->isEmpty())
                        <p class="text-gray-500 text-sm italic">Tidak ada barang yang sedang dalam perjalanan.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="p-3 text-sm text-gray-600 w-32">Tgl Kirim</th>
                                        <th class="p-3 text-sm text-gray-600 w-24">ID Order</th>
                                        <!-- KOLOM TERPISAH: BARANG & JUMLAH -->
                                        <th class="p-3 text-sm text-gray-600">Nama Barang</th>
                                        <th class="p-3 text-sm text-gray-600 text-center w-24">Jumlah</th>
                                        <th class="p-3 text-sm text-gray-600 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($shipped_orders as $order)
                                    <tr class="align-top hover:bg-gray-50">
                                        <!-- Tgl & ID -->
                                        <td class="p-3 text-sm">
                                            <div class="font-semibold text-gray-700">{{ $order->updated_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $order->updated_at->format('H:i') }} WIB</div>
                                        </td>
                                        <td class="p-3 text-sm font-mono text-gray-600">#{{ $order->id }}</td>
                                        
                                        <!-- Nama Barang (List Kebawah) -->
                                        <td class="p-3 text-sm text-gray-700">
                                            @foreach($order->details as $detail)
                                                <!-- Class h-6 (height fixed) agar sejajar dengan kolom jumlah -->
                                                <div class="h-6 overflow-hidden text-ellipsis whitespace-nowrap" title="{{ $detail->product->name }}">
                                                    {{ $detail->product->name ?? 'Unknown Product' }}
                                                </div>
                                            @endforeach
                                        </td>

                                        <!-- Jumlah (List Kebawah) -->
                                        <td class="p-3 text-sm text-center">
                                            @foreach($order->details as $detail)
                                                <div class="h-6 font-bold text-gray-800">
                                                    {{ $detail->quantity }} <span class="text-xs font-normal text-gray-500">{{ $detail->product->unit ?? '' }}</span>
                                                </div>
                                            @endforeach
                                        </td>

                                        <td class="p-3 text-right">
                                            <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                                Sedang Dikirim
                                            </span>
                                            <div class="text-xs text-gray-400 mt-1">Menunggu Konfirmasi</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. SECTION: RIWAYAT TRANSAKSI SELESAI -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg opacity-90">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-history text-gray-400 mr-2"></i>Riwayat Transaksi Selesai
                    </h3>

                    @if($completed_orders->isEmpty())
                        <p class="text-gray-500 text-sm italic">Belum ada riwayat transaksi selesai.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="border-b">
                                    <tr>
                                        <th class="p-3 text-sm text-gray-500 w-32">Tgl Selesai</th>
                                        <th class="p-3 text-sm text-gray-500 w-24">ID Order</th>
                                        <th class="p-3 text-sm text-gray-500">Nama Barang</th>
                                        <th class="p-3 text-sm text-gray-500 text-center w-24">Jumlah</th>
                                        <th class="p-3 text-sm text-gray-500 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($completed_orders as $order)
                                    <tr class="hover:bg-gray-50 align-top">
                                        <td class="p-3 text-sm text-gray-600">{{ $order->updated_at->format('d M Y') }}</td>
                                        <td class="p-3 text-sm font-mono text-gray-600">#{{ $order->id }}</td>
                                        
                                        <!-- Nama Barang -->
                                        <td class="p-3 text-sm text-gray-600">
                                            @foreach($order->details as $detail)
                                                <div class="border-b border-gray-100 last:border-0 py-1">
                                                    {{ $detail->product->name ?? '-' }}
                                                </div>
                                            @endforeach
                                        </td>

                                        <!-- Jumlah -->
                                        <td class="p-3 text-sm text-center text-gray-600">
                                            @foreach($order->details as $detail)
                                                <div class="border-b border-gray-100 last:border-0 py-1 font-semibold">
                                                    {{ $detail->quantity }}
                                                </div>
                                            @endforeach
                                        </td>
                                        
                                        <td class="p-3 text-right">
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full inline-flex items-center gap-1">
                                                <i class="fas fa-check-circle"></i> Selesai
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>