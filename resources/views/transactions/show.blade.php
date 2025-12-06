 <x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Transaksi') }} #{{ $transaction->transaction_number }}
            </h2>
            
            {{-- Badge Status di Header --}}
            @php
                $statusColor = 'bg-gray-100 text-gray-800';
                if (in_array($transaction->status, ['Approved', 'Verified', 'Shipped'])) {
                    $statusColor = 'bg-green-100 text-green-800 border-green-200';
                } elseif ($transaction->status === 'Pending') {
                    $statusColor = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                } elseif ($transaction->status === 'Rejected') {
                    $statusColor = 'bg-red-100 text-red-800 border-red-200';
                }
            @endphp
            <span class="px-4 py-2 rounded-full border {{ $statusColor }} font-bold text-sm shadow-sm">
                {{ strtoupper($transaction->status) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Flash Message --}}
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Bagian Kiri: Informasi Header --}}
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Informasi Umum</h3>
                        
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-gray-500 font-medium">Tipe Transaksi</p>
                                <p class="text-lg font-semibold">
                                    @if($transaction->type === 'incoming')
                                        <span class="text-blue-600 flex items-center">
                                            <i class="fas fa-arrow-down mr-2"></i> Barang Masuk
                                        </span>
                                    @else
                                        <span class="text-red-600 flex items-center">
                                            <i class="fas fa-arrow-up mr-2"></i> Barang Keluar
                                        </span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 font-medium">Tanggal</p>
                                <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d F Y') }}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 font-medium">Dibuat Oleh</p>
                                <p class="font-bold text-gray-800">{{ $transaction->user->name ?? 'Unknown' }}</p>
                            </div>

                            @if($transaction->type === 'incoming')
                            <div>
                                <p class="text-gray-500 font-medium">Supplier</p>
                                <p class="font-bold text-gray-800">{{ $transaction->supplier->name ?? '-' }}</p>
                            </div>
                            @else
                            <div>
                                <p class="text-gray-500 font-medium">Customer</p>
                                <p class="font-bold text-gray-800">{{ $transaction->customer_name ?? '-' }}</p>
                            </div>
                            @endif

                            <div>
                                <p class="text-gray-500 font-medium">Catatan</p>
                                <div class="italic text-gray-600 bg-gray-50 p-3 rounded border text-xs">
                                    {{ $transaction->notes ?: 'Tidak ada catatan.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Kembali --}}
                    <div class="mt-4 flex justify-center">
                        <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900 font-medium text-sm flex items-center underline">
                            &larr; Kembali ke Daftar Transaksi
                        </a>
                    </div>
                </div>

                {{-- Bagian Kanan: Detail & APPROVAL MANAGER --}}
                <div class="md:col-span-2 space-y-6">
                    
                    {{-- Tabel Produk --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Item Produk</h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Produk</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">SKU</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transaction->details as $detail)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $detail->product->name }}</td>
                                            <td class="px-6 py-4 text-sm text-center text-gray-500 font-mono">{{ $detail->product->sku }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $detail->quantity }} {{ $detail->product->unit }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ==================================================== --}}
                    {{-- AREA MANAGER: APPROVAL FORM (Yang Sebelumnya Hilang) --}}
                    {{-- ==================================================== --}}
                    @if ($transaction->status === 'Pending' && (Auth::user()->role === 'manager' || Auth::user()->role === 'admin'))
                        <div class="bg-yellow-50 overflow-hidden shadow-lg sm:rounded-lg p-6 border border-yellow-200 relative">
                            <!-- Hiasan Icon -->
                            <div class="absolute top-0 right-0 p-4 opacity-10">
                                <i class="fas fa-lock text-6xl text-yellow-800"></i>
                            </div>

                            <h3 class="text-lg font-bold text-yellow-900 mb-2 flex items-center">
                                <i class="fas fa-user-shield mr-2"></i> Area Otoritas Manager
                            </h3>
                            <p class="text-sm text-yellow-800 mb-6 max-w-lg">
                                Transaksi ini menunggu persetujuan Anda. 
                                <br>Menyetujui transaksi akan <strong>{{ $transaction->type === 'incoming' ? 'MENAMBAH' : 'MENGURANGI' }}</strong> stok gudang secara otomatis.
                            </p>

                            <div class="flex flex-col sm:flex-row gap-4">
                                {{-- Tombol Approve --}}
                                <form action="{{ route('transactions.approve', $transaction->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Yakin menyetujui transaksi ini? Stok akan berubah.')" 
                                            class="w-full justify-center inline-flex items-center px-4 py-3 bg-green-600 border border-transparent rounded-lg font-bold text-white uppercase tracking-widest hover:bg-green-700 shadow-md transition transform hover:-translate-y-1">
                                        <i class="fas fa-check-circle mr-2"></i> Setujui (Approve)
                                    </button>
                                </form>

                                {{-- Tombol Reject --}}
                                <form action="{{ route('transactions.reject', $transaction->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Yakin menolak transaksi ini?')" 
                                            class="w-full justify-center inline-flex items-center px-4 py-3 bg-rose-600 border border-transparent rounded-lg font-bold text-white uppercase tracking-widest hover:bg-rose-700 shadow-md transition transform hover:-translate-y-1">
                                        <i class="fas fa-times-circle mr-2"></i> Tolak (Reject)
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                    {{-- ==================================================== --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>