@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Restock Order Management') }}
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
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('List Restock Orders') }}</h3>
                        
                        {{-- Tombol Create (Hanya untuk Admin/Manager) --}}
                        @if (Auth::user()->isAdmin() || Auth::user()->isManager())
                            <a href="{{ route('restock_orders.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                {{ __('Create Restock Order') }}
                            </a>
                        @endif
                    </div>

                    {{-- Tabel Restock Orders --}}
                    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected Delivery</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($restockOrders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $order->po_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->supplier->name ?? 'Supplier Dihapus' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->user->name ?? 'Manager Unknown' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->expected_delivery_date ? \Carbon\Carbon::parse($order->expected_delivery_date)->format('d M Y') : '-' }}</td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $statusColor = match ($order->status) {
                                                    'Received' => 'bg-green-100 text-green-800',
                                                    'Confirmed by Supplier' => 'bg-blue-100 text-blue-800',
                                                    'In Transit' => 'bg-purple-100 text-purple-800',
                                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('restock_orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('View') }}</a>
                                            
                                            {{-- Aksi Confirm (Hanya untuk Supplier) --}}
                                            @if (Auth::user()->isSupplier() && $order->status === 'Pending')
                                                <span class="text-xs text-yellow-600">Action Needed</span>
                                            @endif

                                            {{-- Aksi Update Status (Hanya untuk Manager) --}}
                                            @if (Auth::user()->isManager() && in_array($order->status, ['Confirmed by Supplier', 'In Transit']))
                                                <span class="text-xs text-blue-600">Update Status</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('No restock orders found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $restockOrders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection