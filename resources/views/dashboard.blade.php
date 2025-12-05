<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- BAGIAN 1: TAMPILAN KHUSUS ADMIN & MANAGER --}}
            @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                
                {{-- Statistik Utama --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kartu Total Produk -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Total Produk</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $total_products ?? 0 }}</div>
                    </div>

                    <!-- Kartu Stok Menipis (Alert) -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                        <div class="flex justify-between items-center">
                            <div class="text-red-500 text-sm font-bold uppercase">Stok Kritis</div>
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="text-3xl font-bold text-red-600 mt-2">{{ $total_low_stock ?? 0 }}</div>
                        <p class="text-xs text-gray-500 mt-1">Perlu restock segera</p>
                    </div>

                    <!-- Kartu Menunggu Approval -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                        <div class="text-yellow-600 text-sm font-bold uppercase">Butuh Approval</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $pending_transactions ?? 0 }}</div>
                        <p class="text-xs text-gray-500 mt-1">Transaksi Pending</p>
                    </div>

                    <!-- Kartu Transaksi Bulan Ini -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-green-600 text-sm font-medium uppercase">Transaksi (Bulan Ini)</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $transactions_month ?? 0 }}</div>
                    </div>
                </div>

                {{-- Tabel Peringatan & Approval --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Tabel Low Stock Alert -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 bg-red-50 flex justify-between items-center">
                            <h3 class="font-bold text-red-800">⚠️ Peringatan Stok Rendah</h3>
                            <a href="{{ route('products.index') }}" class="text-xs text-red-600 hover:text-red-900 underline">Lihat Semua</a>
                        </div>
                        <div class="p-6">
                            @if(isset($low_stock_products) && count($low_stock_products) > 0)
                                <ul class="divide-y divide-gray-200">
                                    @foreach($low_stock_products as $product)
                                        <li class="py-3 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                                <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2 py-1 text-xs font-bold text-red-100 bg-red-600 rounded-full">
                                                    Sisa: {{ $product->current_stock }} {{ $product->unit }}
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">Semua stok aman.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Tabel Transaksi Pending -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 bg-yellow-50">
                            <h3 class="font-bold text-yellow-800">⏳ Menunggu Persetujuan Manager</h3>
                        </div>
                        <div class="p-6">
                            @if(isset($latest_pending_transactions) && count($latest_pending_transactions) > 0)
                                <ul class="divide-y divide-gray-200">
                                    @foreach($latest_pending_transactions as $trans)
                                        <li class="py-3 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    #{{ $trans->transaction_number }} 
                                                    <span class="text-xs text-gray-500">({{ $trans->type == 'incoming' ? 'Masuk' : 'Keluar' }})</span>
                                                </p>
                                                <p class="text-xs text-gray-500">{{ $trans->user->name ?? 'Staff' }} • {{ \Carbon\Carbon::parse($trans->transaction_date)->format('d M') }}</p>
                                            </div>
                                            <div>
                                                <a href="{{ route('transactions.show', $trans) }}" class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200 font-bold">
                                                    Review
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">Tidak ada transaksi pending.</p>
                            @endif
                        </div>
                    </div>
                </div>

            {{-- BAGIAN 2: TAMPILAN KHUSUS STAFF GUDANG --}}
            @elseif(Auth::user()->isStaff())

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold">Halo, {{ Auth::user()->name }}! 👋</h3>
                        <p class="text-gray-600">Selamat bertugas. Anda memiliki <strong class="text-blue-600">{{ $total_pending_by_me ?? 0 }}</strong> transaksi yang statusnya masih Pending.</p>
                    </div>
                </div>

                {{-- Menu Cepat Staff --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="{{ route('transactions.create_incoming') }}" class="block p-6 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition shadow-sm">
                        <h5 class="text-xl font-bold text-blue-900">📥 Catat Barang Masuk</h5>
                        <p class="text-blue-700 mt-2">Input penerimaan barang dari supplier.</p>
                    </a>
                    
                    <a href="{{ route('transactions.create_outgoing') }}" class="block p-6 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition shadow-sm">
                        <h5 class="text-xl font-bold text-red-900">📤 Catat Barang Keluar</h5>
                        <p class="text-red-700 mt-2">Input pengiriman barang ke customer.</p>
                    </a>
                </div>

                {{-- Riwayat Terakhir Staff --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="font-bold text-gray-800">Aktivitas Terakhir Saya</h3>
                    </div>
                    <div class="overflow-x-auto p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">No. Transaksi</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($my_recent_transactions ?? [] as $trans)
                                    <tr>
                                        <td class="py-3 text-sm font-medium text-gray-900">
                                            <a href="{{ route('transactions.show', $trans) }}" class="hover:text-blue-600 hover:underline">
                                                {{ $trans->transaction_number }}
                                            </a>
                                        </td>
                                        <td class="py-3 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $trans->type == 'incoming' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($trans->type) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $trans->status == 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $trans->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($trans->transaction_date)->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">Belum ada aktivitas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            {{-- BAGIAN 3: TAMPILAN KHUSUS SUPPLIER --}}
            @elseif(Auth::user()->isSupplier())
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                        <div class="text-orange-600 text-sm font-bold uppercase">Pesanan Perlu Konfirmasi</div>
                        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $orders_to_confirm ?? 0 }}</div>
                    </div>
                </div>

                {{-- Tabel Pesanan Terbaru --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="font-bold text-gray-800">Pesanan Restock Terbaru (Dari Gudang)</h3>
                    </div>
                    <div class="p-6">
                         @if(isset($latest_orders) && count($latest_orders) > 0)
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="text-left text-xs font-medium text-gray-500 uppercase">No. PO</th>
                                        <th class="text-left text-xs font-medium text-gray-500 uppercase">Tanggal Order</th>
                                        <th class="text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latest_orders as $order)
                                        <tr>
                                            <td class="py-3 text-sm font-medium">{{ $order->po_number }}</td>
                                            <td class="py-3 text-sm">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
                                            <td class="py-3 text-sm">{{ $order->status }}</td>
                                            <td class="py-3 text-sm">
                                                <a href="{{ route('restock_orders.show', $order) }}" class="text-blue-600 hover:underline">Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-500 text-center">Belum ada pesanan restock.</p>
                        @endif
                    </div>
                </div>

            @endif

            {{-- MENU NAVIGASI UMUM (DIPERBAIKI: Filter Akses Berdasarkan Role) --}}
            @if(!Auth::user()->isSupplier())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="font-bold text-gray-800">Menu Cepat Sistem</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- 1. Transaksi (Semua Role Internal: Admin, Manager, Staff) --}}
                        <a href="{{ route('transactions.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <span class="text-2xl mr-3">📋</span>
                            <div>
                                <h4 class="font-bold text-gray-700">Manajemen Transaksi</h4>
                                <p class="text-xs text-gray-500">Lihat riwayat penuh</p>
                            </div>
                        </a>

                        {{-- 2. Produk & Stok (HANYA Admin & Manager) --}}
                        @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                            <a href="{{ route('products.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <span class="text-2xl mr-3">🏷️</span>
                                <div>
                                    <h4 class="font-bold text-gray-700">Produk & Stok</h4>
                                    <p class="text-xs text-gray-500">Master data barang</p>
                                </div>
                            </a>
                        @endif

                        {{-- 3. Restock Order (HANYA Admin & Manager) --}}
                        @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                            <a href="{{ route('restock_orders.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <span class="text-2xl mr-3">🚚</span>
                                <div>
                                    <h4 class="font-bold text-gray-700">Restock Order</h4>
                                    <p class="text-xs text-gray-500">Pesanan ke supplier</p>
                                </div>
                            </a>
                        @endif

                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>