@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Transaction Detail') }}: #{{ $transaction->transaction_number }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Notifikasi Status --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Header dan Status Transaksi --}}
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-900">
                            Transaction #{{ $transaction->transaction_number }} 
                            <span class="text-sm font-normal text-gray-500">({{ $transaction->type === 'incoming' ? 'Barang Masuk' : 'Barang Keluar' }})</span>
                        </h3>
                        
                        @php
                            $statusColor = match ($transaction->status) {
                                'Approved', 'Verified', 'Shipped' => 'bg-green-600',
                                'Pending' => 'bg-yellow-500',
                                'Rejected' => 'bg-red-600',
                                default => 'bg-gray-500',
                            };
                        @endphp
                        
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white {{ $statusColor }}">
                            {{ strtoupper($transaction->status) }}
                        </span>
                    </div>

                    {{-- Informasi Utama --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="space-y-2">
                            <h4 class="font-semibold text-gray-700">{{ __('Date') }}</h4>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-semibold text-gray-700">{{ __('Initiated By') }}</h4>
                            <p class="text-gray-900">{{ $transaction->user->name ?? 'User Unknown' }} (Staff)</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-semibold text-gray-700">{{ $transaction->type === 'incoming' ? 'Supplier' : 'Customer/Destination' }}</h4>
                            @if ($transaction->type === 'incoming')
                                <p class="text-gray-900">{{ $transaction->supplier->name ?? 'Supplier Dihapus' }}</p>
                            @else
                                <p class="text-gray-900">{{ $transaction->customer_name }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <h4 class="font-semibold text-gray-700 border-t pt-4">{{ __('Notes') }}</h4>
                    <p class="text-gray-600 mt-1 mb-8">{{ $transaction->notes ?? '-' }}</p>

                    {{-- Daftar Produk --}}
                    <h4 class="text-xl font-bold text-gray-900 mb-4 border-t pt-4">{{ __('Product Items') }}</h4>

                    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg mb-8">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name (SKU)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($transaction->details as $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $detail->product->name ?? 'Product Not Found' }} ({{ $detail->product->sku ?? 'N/A' }})
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $detail->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $detail->product->unit ?? 'pcs' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Tombol Approval (Hanya untuk Manager dan Status Pending) --}}
                    @if (Auth::user()->isManager() && $transaction->status === 'Pending')
                        <div class="border-t pt-4 flex justify-end space-x-3">
                            {{-- Tombol Tolak --}}
                            <form action="{{ route('transactions.reject', $transaction) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK Transaksi #{{ $transaction->transaction_number }}?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Reject Transaction') }}
                                </button>
                            </form>

                            {{-- Tombol Setuju --}}
                            <form action="{{ route('transactions.approve', $transaction) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI Transaksi #{{ $transaction->transaction_number }}? Stok akan diperbarui.');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Approve & Update Stock') }}
                                </button>
                            </form>
                        </div>
                    @endif
                    
                    {{-- Tombol Kembali --}}
                    <div class="mt-4 flex justify-start">
                        <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                            &larr; {{ __('Back to Transactions List') }}
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection