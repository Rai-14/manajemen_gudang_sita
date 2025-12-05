<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Manajemen Restock Order') }}
            </h2>
            
            {{-- Tombol Create (Hanya untuk Admin/Manager) --}}
            @if (Auth::user()->isAdmin() || Auth::user()->isManager())
                <a href="{{ route('restock_orders.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-orange-700 active:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Buat PO Baru') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 shadow-sm rounded-r flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 shadow-sm rounded-r flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-lg">Daftar Pesanan Pembelian (PO)</h3>
                    <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-md">Total: {{ $restockOrders->total() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No. PO</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Order</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estimasi Tiba</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse ($restockOrders as $order)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-slate-700">
                                        {{ $order->po_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-700">
                                        {{ $order->supplier->name ?? 'Deleted' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        {{ $order->expected_delivery_date ? \Carbon\Carbon::parse($order->expected_delivery_date)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusClass = match($order->status) {
                                                'Received' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'Confirmed by Supplier' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'In Transit' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                'Pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                default => 'bg-slate-100 text-slate-800'
                                            };
                                        @endphp
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full border {{ $statusClass }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('restock_orders.show', $order) }}" class="text-orange-600 hover:text-orange-900 font-bold hover:underline">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                            <p>Belum ada pesanan restock.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
                    {{ $restockOrders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>