<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Restock Order') }} #{{ $restockOrder->po_number }}
            </h2>
            
            @php
                $statusColor = match ($restockOrder->status) {
                    'Received' => 'bg-green-100 text-green-800',
                    'Confirmed by Supplier' => 'bg-blue-100 text-blue-800',
                    'In Transit' => 'bg-orange-100 text-orange-800',
                    'Pending' => 'bg-yellow-100 text-yellow-800',
                    default => 'bg-gray-100 text-gray-800',
                };
            @endphp
            <span class="px-4 py-2 rounded-full border {{ $statusColor }} font-bold text-sm shadow-sm">
                {{ strtoupper($restockOrder->status) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Kiri: Info Header --}}
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Informasi Order</h3>
                        <div class="space-y-4 text-sm">
                            <div><p class="text-gray-500 font-bold">Supplier</p><p>{{ $restockOrder->supplier->name }}</p></div>
                            <div><p class="text-gray-500 font-bold">Tanggal Order</p><p>{{ \Carbon\Carbon::parse($restockOrder->order_date)->format('d M Y') }}</p></div>
                            <div><p class="text-gray-500 font-bold">Estimasi Tiba</p><p>{{ $restockOrder->expected_delivery_date ? \Carbon\Carbon::parse($restockOrder->expected_delivery_date)->format('d M Y') : '-' }}</p></div>
                            <div><p class="text-gray-500 font-bold">Manager Pembuat</p><p>{{ $restockOrder->user->name ?? 'Unknown' }}</p></div>
                            <div><p class="text-gray-500 font-bold">Catatan</p><p class="italic bg-gray-50 p-2 rounded border">{{ $restockOrder->notes ?? '-' }}</p></div>
                        </div>
                    </div>

                    {{-- AREA TINDAKAN --}}
                    
                    {{-- 1. Supplier: Konfirmasi --}}
                    @if(Auth::user()->isSupplier() && $restockOrder->status === 'Pending')
                        <div class="bg-orange-50 p-6 border border-orange-200 rounded-lg shadow-sm">
                            <h3 class="font-bold text-orange-900 mb-2">Tindakan Supplier</h3>
                            <form action="{{ route('restock_orders.confirm', $restockOrder) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full bg-orange-600 text-white px-4 py-2 rounded font-bold hover:bg-orange-700 shadow" onclick="return confirm('Konfirmasi ketersediaan barang?')">
                                    ✅ Konfirmasi Order
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- 2. Manager: Update Status --}}
                    @if(Auth::user()->isManager() || Auth::user()->isAdmin())
                        @if($restockOrder->status === 'Confirmed by Supplier')
                            <div class="bg-blue-50 p-6 border border-blue-200 rounded-lg shadow-sm">
                                <h3 class="font-bold text-blue-900 mb-2">Update Pengiriman</h3>
                                <p class="text-xs text-blue-700 mb-3">Klik jika barang sudah dikirim Supplier.</p>
                                <form action="{{ route('restock_orders.update_status', $restockOrder) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="In Transit">
                                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-blue-700 shadow">
                                        🚚 Set: In Transit
                                    </button>
                                </form>
                            </div>
                        @elseif($restockOrder->status === 'In Transit')
                            <div class="bg-green-50 p-6 border border-green-200 rounded-lg shadow-sm">
                                <h3 class="font-bold text-green-900 mb-2">Penerimaan Barang</h3>
                                <p class="text-xs text-green-700 mb-3">Klik jika barang sudah tiba di gudang.</p>
                                <form action="{{ route('restock_orders.update_status', $restockOrder) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="Received">
                                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded font-bold hover:bg-green-700 shadow">
                                        📦 Set: Received (Diterima)
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Kanan: Detail Barang --}}
                <div class="md:col-span-2 bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="text-lg font-bold mb-4">Item Produk</h3>
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Produk</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">SKU</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Jumlah Order</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($restockOrder->details as $detail)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium">{{ $detail->product->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $detail->product->sku }}</td>
                                    <td class="px-4 py-3 text-sm font-bold bg-gray-50">{{ $detail->quantity }} {{ $detail->product->unit }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>