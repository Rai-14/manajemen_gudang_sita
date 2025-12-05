<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Detail Restock Order') }} <span class="font-mono text-slate-500 text-lg">#{{ $restockOrder->po_number }}</span>
            </h2>
            
            @php
                $statusColor = match ($restockOrder->status) {
                    'Received' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'Confirmed by Supplier' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'In Transit' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'Pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                    default => 'bg-slate-100 text-slate-800',
                };
            @endphp
            <span class="px-4 py-1.5 rounded-full border {{ $statusColor }} font-bold text-sm shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-current opacity-75"></span>
                {{ strtoupper($restockOrder->status) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r shadow-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r shadow-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Kiri: Info Header & Actions --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-5 border-b border-slate-100 bg-slate-50">
                            <h3 class="font-bold text-slate-800">Informasi Pesanan</h3>
                        </div>
                        <div class="p-5 space-y-4 text-sm">
                            <div class="flex justify-between border-b border-slate-100 pb-3">
                                <span class="text-slate-500">Supplier</span>
                                <span class="font-bold text-slate-800">{{ $restockOrder->supplier->name }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-3">
                                <span class="text-slate-500">Tanggal Order</span>
                                <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($restockOrder->order_date)->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-3">
                                <span class="text-slate-500">Estimasi Tiba</span>
                                <span class="font-bold text-slate-800">{{ $restockOrder->expected_delivery_date ? \Carbon\Carbon::parse($restockOrder->expected_delivery_date)->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Dibuat Oleh</span>
                                <span class="font-bold text-slate-800">{{ $restockOrder->user->name ?? 'Unknown' }}</span>
                            </div>
                        </div>
                        
                        @if($restockOrder->notes)
                            <div class="p-5 bg-amber-50 border-t border-amber-100">
                                <h4 class="text-xs font-bold text-amber-600 uppercase mb-1">Catatan</h4>
                                <p class="text-sm text-amber-800 italic">"{{ $restockOrder->notes }}"</p>
                            </div>
                        @endif
                    </div>

                    {{-- ACTION PANEL --}}
                    
                    {{-- 1. Supplier: Konfirmasi --}}
                    @if(Auth::user()->isSupplier() && $restockOrder->status === 'Pending')
                        <div class="bg-white rounded-xl shadow-lg border border-orange-200 overflow-hidden ring-1 ring-orange-100">
                            <div class="p-5 bg-orange-50 border-b border-orange-100">
                                <h3 class="font-bold text-orange-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tindakan Supplier
                                </h3>
                                <p class="text-xs text-orange-600 mt-1">Konfirmasi ketersediaan stok barang.</p>
                            </div>
                            <div class="p-5">
                                <form action="{{ route('restock_orders.confirm', $restockOrder) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-full bg-orange-600 text-white px-4 py-3 rounded-lg font-bold hover:bg-orange-700 shadow-md transition transform hover:-translate-y-0.5" onclick="return confirm('Konfirmasi pesanan ini?')">
                                        ✅ Konfirmasi Pesanan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- 2. Manager: Update Status --}}
                    @if(Auth::user()->isManager() || Auth::user()->isAdmin())
                        @if($restockOrder->status === 'Confirmed by Supplier')
                            <div class="bg-white rounded-xl shadow-md border border-blue-200 overflow-hidden">
                                <div class="p-5 bg-blue-50 border-b border-blue-100">
                                    <h3 class="font-bold text-blue-900">Update Pengiriman</h3>
                                    <p class="text-xs text-blue-700 mt-1">Klik jika barang sudah dikirim Supplier.</p>
                                </div>
                                <div class="p-5">
                                    <form action="{{ route('restock_orders.update_status', $restockOrder) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="In Transit">
                                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg font-bold hover:bg-blue-700 shadow transition">
                                            🚚 Set Status: Dikirim
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif($restockOrder->status === 'In Transit')
                            <div class="bg-white rounded-xl shadow-md border border-emerald-200 overflow-hidden">
                                <div class="p-5 bg-emerald-50 border-b border-emerald-100">
                                    <h3 class="font-bold text-emerald-900">Penerimaan Barang</h3>
                                    <p class="text-xs text-emerald-700 mt-1">Klik jika barang sudah tiba di gudang.</p>
                                </div>
                                <div class="p-5">
                                    <form action="{{ route('restock_orders.update_status', $restockOrder) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="Received">
                                        <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-3 rounded-lg font-bold hover:bg-emerald-700 shadow transition">
                                            📦 Set Status: Diterima
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Kanan: Detail Barang --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-6 border-b border-slate-100">
                            <h3 class="font-bold text-slate-800 text-lg">Rincian Item Produk</h3>
                        </div>
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah Order</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($restockOrder->details as $detail)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-900">{{ $detail->product->name }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">{{ $detail->product->category->name ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500 font-mono">
                                            {{ $detail->product->sku }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                                {{ $detail->quantity }} {{ $detail->product->unit }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="p-6 bg-slate-50 border-t border-slate-200 text-right">
                            <p class="text-sm text-slate-500">Total Item: <span class="font-bold text-slate-800">{{ $restockOrder->details->count() }}</span></p>
                        </div>
                    </div>

                    <div class="mt-6 text-right">
                        <a href="{{ route('restock_orders.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm hover:underline">
                            &larr; Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>