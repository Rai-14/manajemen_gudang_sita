<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Dashboard Overview') }}
            </h2>
            <div class="text-sm text-slate-500">
                {{ now()->format('l, d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- BAGIAN 1: TAMPILAN KHUSUS ADMIN & MANAGER --}}
            @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                
                {{-- Statistik Utama --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kartu Total Produk -->
                    <div class="bg-white rounded-xl p-6 border-l-4 border-blue-500 shadow-sm hover:shadow-md transition">
                        <div class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Aset Produk</div>
                        <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $total_products ?? 0 }}</div>
                        <div class="text-xs text-slate-400 mt-1">Item terdaftar di sistem</div>
                    </div>

                    <!-- Kartu Stok Menipis (Alert) -->
                    <div class="bg-white rounded-xl p-6 border-l-4 border-rose-500 shadow-sm hover:shadow-md transition">
                        <div class="flex justify-between items-center">
                            <div class="text-rose-500 text-xs font-bold uppercase tracking-wider">Stok Kritis</div>
                            <div class="p-1.5 bg-rose-50 rounded-full">
                                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-extrabold text-rose-600 mt-2">{{ $total_low_stock ?? 0 }}</div>
                        <p class="text-xs text-rose-400 mt-1 font-medium">Perlu restock segera</p>
                    </div>

                    <!-- Kartu Menunggu Approval -->
                    <div class="bg-white rounded-xl p-6 border-l-4 border-amber-500 shadow-sm hover:shadow-md transition">
                        <div class="flex justify-between items-center">
                            <div class="text-amber-600 text-xs font-bold uppercase tracking-wider">Butuh Approval</div>
                            <div class="p-1.5 bg-amber-50 rounded-full">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $pending_transactions ?? 0 }}</div>
                        <p class="text-xs text-slate-400 mt-1">Transaksi Pending</p>
                    </div>

                    <!-- Kartu Transaksi Bulan Ini -->
                    <div class="bg-white rounded-xl p-6 border-l-4 border-emerald-500 shadow-sm hover:shadow-md transition">
                        <div class="text-emerald-600 text-xs font-bold uppercase tracking-wider">Aktivitas (Bulan Ini)</div>
                        <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $transactions_month ?? 0 }}</div>
                        <p class="text-xs text-slate-400 mt-1">Transaksi Keluar/Masuk</p>
                    </div>
                </div>

                <!-- Kartu Nilai Inventori -->
                <div class="bg-white rounded-xl p-6 border-l-4 border-indigo-500 shadow-sm hover:shadow-md transition">
                    <div class="text-indigo-600 text-xs font-bold uppercase tracking-wider">Nilai Aset Gudang</div>
                    
                    <!-- Bagian Menampilkan Angka -->
                    <div class="text-2xl font-extrabold text-slate-800 mt-2">
                        {{-- Format Rupiah: Rp 150.000.000 --}}
                        Rp {{ number_format($total_inventory_value ?? 0, 0, ',', '.') }}
                    </div>
                    
                    <p class="text-xs text-indigo-400 mt-1">Estimasi Harga Beli (Cost)</p>
                </div>

                {{-- Tabel Peringatan & Approval --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Tabel Low Stock Alert -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-4 border-b border-slate-100 bg-rose-50/50 flex justify-between items-center">
                            <h3 class="font-bold text-rose-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Peringatan Stok Rendah
                            </h3>
                            <a href="{{ route('products.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-800">Lihat Semua &rarr;</a>
                        </div>
                        <div class="p-0">
                            @if(isset($low_stock_products) && count($low_stock_products) > 0)
                                <ul class="divide-y divide-slate-100">
                                    @foreach($low_stock_products as $product)
                                        <li class="px-4 py-3 flex justify-between items-center hover:bg-slate-50 transition">
                                            <div>
                                                <p class="text-sm font-bold text-slate-700">{{ $product->name }}</p>
                                                <p class="text-xs text-slate-400 font-mono">{{ $product->sku }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                    Sisa: {{ $product->current_stock }} {{ $product->unit }}
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="p-8 text-center">
                                    <div class="inline-flex bg-emerald-100 p-3 rounded-full mb-3 text-emerald-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-sm text-slate-500">Semua stok aman terkendali.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tabel Transaksi Pending -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-4 border-b border-slate-100 bg-amber-50/50">
                            <h3 class="font-bold text-amber-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Menunggu Persetujuan
                            </h3>
                        </div>
                        <div class="p-0">
                            @if(isset($latest_pending_transactions) && count($latest_pending_transactions) > 0)
                                <ul class="divide-y divide-slate-100">
                                    @foreach($latest_pending_transactions as $trans)
                                        <li class="px-4 py-3 flex justify-between items-center hover:bg-slate-50 transition">
                                            <div>
                                                <p class="text-sm font-bold text-slate-700">
                                                    #{{ $trans->transaction_number }} 
                                                </p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-xs px-2 py-0.5 rounded {{ $trans->type == 'incoming' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                                        {{ $trans->type == 'incoming' ? 'Masuk' : 'Keluar' }}
                                                    </span>
                                                    <span class="text-xs text-slate-400">{{ $trans->user->name ?? 'Staff' }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <a href="{{ route('transactions.show', $trans) }}" class="text-xs font-bold bg-slate-800 text-white px-3 py-1.5 rounded-lg hover:bg-slate-700 shadow-sm transition">
                                                    Review
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="p-8 text-center">
                                    <div class="inline-flex bg-slate-100 p-3 rounded-full mb-3 text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-sm text-slate-500">Tidak ada transaksi pending saat ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            {{-- BAGIAN 2: TAMPILAN KHUSUS STAFF GUDANG --}}
            @elseif(Auth::user()->isStaff())

                <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Halo, {{ Auth::user()->name }}! 👋</h3>
                            <p class="text-slate-500">Selamat bertugas. Anda memiliki <strong class="text-amber-600">{{ $total_pending_by_me ?? 0 }}</strong> transaksi yang statusnya masih Pending.</p>
                        </div>
                    </div>
                </div>

                {{-- Menu Cepat Staff --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="{{ route('transactions.create_incoming') }}" class="group block p-6 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:shadow-md transition cursor-pointer">
                        <div class="flex items-center justify-between">
                            <h5 class="text-xl font-bold text-slate-800 group-hover:text-blue-600 transition">📥 Catat Barang Masuk</h5>
                            <div class="bg-blue-50 text-blue-600 p-2 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                        </div>
                        <p class="text-slate-500 mt-2 text-sm">Input penerimaan barang dari supplier ke gudang.</p>
                    </a>
                    
                    <a href="{{ route('transactions.create_outgoing') }}" class="group block p-6 bg-white border border-slate-200 rounded-xl hover:border-red-400 hover:shadow-md transition cursor-pointer">
                        <div class="flex items-center justify-between">
                            <h5 class="text-xl font-bold text-slate-800 group-hover:text-red-600 transition">📤 Catat Barang Keluar</h5>
                            <div class="bg-red-50 text-red-600 p-2 rounded-lg group-hover:bg-red-600 group-hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </div>
                        </div>
                        <p class="text-slate-500 mt-2 text-sm">Input pengiriman barang ke customer atau produksi.</p>
                    </a>
                </div>

                {{-- Riwayat Terakhir Staff --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 mt-6 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800">Aktivitas Terakhir Saya</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">No. Transaksi</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($my_recent_transactions ?? [] as $trans)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 text-sm font-medium text-slate-900">
                                            <a href="{{ route('transactions.show', $trans) }}" class="text-blue-600 hover:underline font-mono">
                                                {{ $trans->transaction_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $trans->type == 'incoming' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                                {{ $trans->type == 'incoming' ? 'Masuk' : 'Keluar' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $trans->status == 'Pending' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                                {{ $trans->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">{{ \Carbon\Carbon::parse($trans->transaction_date)->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada aktivitas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            {{-- BAGIAN 3: TAMPILAN KHUSUS SUPPLIER --}}
            @elseif(Auth::user()->isSupplier())
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl p-6 border-l-4 border-orange-500 shadow-sm">
                        <div class="text-orange-600 text-xs font-bold uppercase tracking-wider">Pesanan Perlu Konfirmasi</div>
                        <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $orders_to_confirm ?? 0 }}</div>
                        <p class="text-xs text-slate-400 mt-1">PO Menunggu</p>
                    </div>
                </div>

                {{-- Tabel Pesanan Terbaru --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 mt-6 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800">Pesanan Restock Terbaru (Dari Gudang)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        @if(isset($latest_orders) && count($latest_orders) > 0)
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">No. PO</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    @foreach($latest_orders as $order)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 text-sm font-mono font-bold text-slate-700">{{ $order->po_number }}</td>
                                            <td class="px-6 py-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $order->status == 'Pending' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $order->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <a href="{{ route('restock_orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-bold hover:underline">Detail & Konfirmasi</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-8 text-center text-slate-400">Belum ada pesanan restock.</div>
                        @endif
                    </div>
                </div>

            @endif

            {{-- MENU NAVIGASI UMUM (Hanya Non-Supplier) --}}
            @if(!Auth::user()->isSupplier())
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 mt-8">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800">Akses Cepat Modul</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        <a href="{{ route('transactions.index') }}" class="flex items-center p-4 bg-slate-50 rounded-xl hover:bg-white hover:shadow-md hover:border-blue-200 border border-transparent transition group">
                            <span class="text-2xl mr-4 grayscale group-hover:grayscale-0 transition">📋</span>
                            <div>
                                <h4 class="font-bold text-slate-700 group-hover:text-blue-600 transition">Manajemen Transaksi</h4>
                                <p class="text-xs text-slate-500">History keluar/masuk</p>
                            </div>
                        </a>

                        {{-- Hanya Admin & Manager --}}
                        @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                            <a href="{{ route('products.index') }}" class="flex items-center p-4 bg-slate-50 rounded-xl hover:bg-white hover:shadow-md hover:border-blue-200 border border-transparent transition group">
                                <span class="text-2xl mr-4 grayscale group-hover:grayscale-0 transition">🏷️</span>
                                <div>
                                <h4 class="font-bold text-slate-700 group-hover:text-blue-600 transition">Produk & Stok</h4>
                                    <p class="text-xs text-slate-500">Master data barang</p>
                                </div>
                            </a>

                            <a href="{{ route('restock_orders.index') }}" class="flex items-center p-4 bg-slate-50 rounded-xl hover:bg-white hover:shadow-md hover:border-blue-200 border border-transparent transition group">
                                <span class="text-2xl mr-4 grayscale group-hover:grayscale-0 transition">🚚</span>
                                <div>
                                    <h4 class="font-bold text-slate-700 group-hover:text-blue-600 transition">Restock Order</h4>
                                    <p class="text-xs text-slate-500">Pesanan supplier</p>
                                </div>
                            </a>
                        @endif

                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>