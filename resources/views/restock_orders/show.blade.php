@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Restock Order Detail') }}: #{{ $restockOrder->po_number }}
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
                    
                    {{-- Header dan Status Pesanan --}}
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-900">
                            Purchase Order #{{ $restockOrder->po_number }}
                        </h3>
                        
                        @php
                            $statusColor = match ($restockOrder->status) {
                                'Received' => 'bg-green-600',
                                'Confirmed by Supplier' => 'bg-blue-600',
                                'In Transit' => 'bg-purple-600',
                                'Pending' => 'bg-yellow-500',
                                default => 'bg-gray-500',
                            };
                        @endphp
                        
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white {{ $statusColor }}">
                            {{ strtoupper($restockOrder->status) }}
                        </span>
                    </div>

                    {{-- Informasi Utama --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="space-y-2">
                            <h4 class="font-semibold text-gray-700">{{ __('Order Date') }}</h4>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($restockOrder->order_date)->format('d M Y') }}</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-semibold text-gray-700">{{ __('Expected Delivery') }}</h4>
                            <p class="text-gray-900">{{ $restockOrder->expected_delivery_date ? \Carbon\Carbon::parse($restockOrder->expected_delivery_date)->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-semibold text-gray-700">{{ __('Supplier') }}</h4>
                            <p class="text-gray-900">{{ $restockOrder->supplier->name ?? 'Supplier Dihapus' }}</p>
                        </div>
                        <div class="md:col-span-3 space-y-2">
                            <h4 class="font-semibold text-gray-700">{{ __('Manager') }}</h4>
                            <p class="text-gray-900">{{ $restockOrder->user->name ?? 'User Unknown' }}</p>
                        </div>
                    </div>
                    
                    <h4 class="font-semibold text-gray-700 border-t pt-4">{{ __('Notes') }}</h4>
                    <p class="text-gray-600 mt-1 mb-8">{{ $restockOrder->notes ?? '-' }}</p>

                    {{-- Daftar Produk --}}
                    <h4 class="text-xl font-bold text-gray-900 mb-4 border-t pt-4">{{ __('Products in Order') }}</h4>

                    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg mb-8">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name (SKU)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Requested</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($restockOrder->details as $detail)
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

                    {{-- Tombol Aksi (Supplier) --}}
                    @if (Auth::user()->isSupplier() && Auth::user()->supplier_id === $restockOrder->supplier_id && $restockOrder->status === 'Pending')
                        <div class="border-t pt-4 flex justify-end space-x-3">
                            {{-- Tombol Konfirmasi (Kita akan menggunakan modal untuk menolak di Langkah 24) --}}
                            <form action="{{ route('restock_orders.confirm', $restockOrder) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin MENGKONFIRMASI pesanan #{{ $restockOrder->po_number }}?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" name="action" value="confirm" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Confirm Availability') }}
                                </button>
                            </form>
                            
                            {{-- Tombol Tolak/Tunda (Untuk diselesaikan di Langkah 24) --}}
                            <button type="button" onclick="alert('Fungsi Reject/Delay akan ditambahkan di langkah berikutnya.')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Reject/Delay') }}
                            </button>
                        </div>
                    @endif
                    
                    {{-- Tombol Aksi (Manager) --}}
                    @if (Auth::user()->isManager() || Auth::user()->isAdmin())
                        @if (in_array($restockOrder->status, ['Confirmed by Supplier', 'In Transit']))
                            <div class="border-t pt-4 flex justify-end space-x-3">
                                {{-- Tombol Update Status (Menggunakan form select/modal di Langkah 24) --}}
                                <form action="{{ route('restock_orders.update_status', $restockOrder) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    
                                    <select name="status" required class="border-gray-300 rounded-md shadow-sm text-sm mr-3">
                                        <option value="">-- Change Status To --</option>
                                        @if ($restockOrder->status === 'Confirmed by Supplier')
                                            <option value="In Transit">{{ __('In Transit') }}</option>
                                        @endif
                                        @if ($restockOrder->status === 'In Transit')
                                            <option value="Received">{{ __('Received (Create Incoming Trans)') }}</option>
                                        @endif
                                    </select>
                                    
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        @if ($restockOrder->status === 'Received') disabled @endif>
                                        {{ __('Update Status') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                    
                    {{-- Tombol Kembali --}}
                    <div class="mt-4 flex justify-start">
                        <a href="{{ route('restock_orders.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                            &larr; {{ __('Back to Restock Orders List') }}
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection