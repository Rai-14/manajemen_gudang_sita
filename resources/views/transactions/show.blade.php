<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Transaksi') }} #{{ $transaction->transaction_number }}
            </h2>
            
            {{-- Badge Status di Header --}}
            @php
                $statusColor = match ($transaction->status) {
                    'Approved', 'Verified', 'Shipped' => 'bg-green-100 text-green-800 border-green-200',
                    'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'Rejected' => 'bg-red-100 text-red-800 border-red-200',
                    default => 'bg-gray-100 text-gray-800',
                };
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
                
                {{-- Bagian Kiri: Informasi Header Transaksi --}}
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Informasi Umum</h3>
                        
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-gray-500 font-medium">Tipe Transaksi</p>
                                <p class="text-lg font-semibold">
                                    @if($transaction->type === 'incoming')
                                        <span class="text-blue-600 flex items-center">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                            Barang Masuk
                                        </span>
                                    @else
                                        <span class="text-red-600 flex items-center">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                            Barang Keluar
                                        </span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 font-medium">Tanggal</p>
                                <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d F Y') }}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 font-medium">Dibuat Oleh (Staff)</p>
                                <p class="font-bold text-gray-800">{{ $transaction->user->name ?? 'Unknown' }}</p>
                            </div>

                            @if($transaction->type === 'incoming')
                            <div>
                                <p class="text-gray-500 font-medium">Supplier</p>
                                <p class="font-bold text-gray-800">{{ $transaction->supplier->name ?? '-' }}</p>
                            </div>
                            @else
                            <div>
                                <p class="text-gray-500 font-medium">Customer / Tujuan</p>
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

                    {{-- AREA MANAGER: APPROVAL (Hanya Tampil Jika Pending & User adalah Manager/Admin) --}}
                    @if ((Auth::user()->isManager() || Auth::user()->isAdmin()) && $transaction->status === 'Pending')
                        <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-blue-200">
                            <h3 class="text-lg font-bold text-blue-900 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Tindakan Manajer
                            </h3>
                            <p class="text-sm text-blue-700 mb-4">
                                Transaksi ini menunggu persetujuan Anda. Menyetujui transaksi akan 
                                <strong>{{ $transaction->type === 'incoming' ? 'MENAMBAH' : 'MENGURANGI' }}</strong> stok produk secara otomatis.
                            </p>

                            <div class="flex flex-col gap-3">
                                {{-- Tombol Approve --}}
                                <form action="{{ route('transactions.approve', $transaction) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui transaksi ini? Stok akan diperbarui.')" 
                                        class="w-full justify-center inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring ring-green-300 transition ease-in-out duration-150 shadow-md">
                                        ✅ Setujui (Approve)
                                    </button>
                                </form>

                                {{-- Tombol Reject --}}
                                <form action="{{ route('transactions.reject', $transaction) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak transaksi ini?')" 
                                        class="w-full justify-center inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring ring-red-300 transition ease-in-out duration-150 shadow-md">
                                        ❌ Tolak (Reject)
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Tombol Kembali --}}
                    <div class="mt-4 flex justify-center">
                        <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900 font-medium text-sm flex items-center underline">
                            &larr; Kembali ke Daftar Transaksi
                        </a>
                    </div>
                </div>

                {{-- Bagian Kanan: Detail Produk (Tabel) --}}
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Item Produk</h3>

                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">SKU</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Satuan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transaction->details as $detail)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $detail->product->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="px-2 py-1 bg-gray-100 rounded text-xs font-mono">{{ $detail->product->sku }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $detail->quantity }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $detail->product->unit }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>