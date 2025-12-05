<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Manajemen Transaksi') }}
            </h2>
            <div class="flex items-center gap-2">
                {{-- Tombol Transaksi Baru (Hanya Staff) --}}
                @if (Auth::user()->isStaff())
                    <a href="{{ route('transactions.create') }}" class="inline-flex items-center bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        {{ __('Transaksi Baru') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Notifikasi --}}
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

            {{-- Filter Toolbar --}}
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                <form method="GET" action="{{ route('transactions.index') }}">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        
                        <div class="w-full md:w-1/4">
                            <label for="type" class="block text-xs font-bold text-slate-500 uppercase mb-1">Filter Tipe</label>
                            <select name="type" id="type" onchange="this.form.submit()" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Tipe</option>
                                <option value="incoming" @selected(request('type') == 'incoming')>📥 Barang Masuk</option>
                                <option value="outgoing" @selected(request('type') == 'outgoing')>📤 Barang Keluar</option>
                            </select>
                        </div>
                        
                        <div class="w-full md:w-1/4">
                            <label for="status" class="block text-xs font-bold text-slate-500 uppercase mb-1">Filter Status</label>
                            <select name="status" id="status" onchange="this.form.submit()" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="Pending" @selected(request('status') == 'Pending')>⏳ Pending</option>
                                <option value="Verified" @selected(request('status') == 'Verified')>✅ Verified</option>
                                <option value="Approved" @selected(request('status') == 'Approved')>✅ Approved</option>
                                <option value="Rejected" @selected(request('status') == 'Rejected')>❌ Rejected</option>
                                @if(Auth::user()->isManager() || Auth::user()->isAdmin())
                                    <option value="pending_approval" @selected(request('status') == 'pending_approval')>⚠️ Perlu Persetujuan Saya</option>
                                @endif
                            </select>
                        </div>

                        @if (request()->has('type') || request()->has('status'))
                            <a href="{{ route('transactions.index') }}" class="bg-slate-100 text-slate-600 hover:bg-slate-200 px-4 py-2.5 rounded-lg text-sm font-bold transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabel Transaksi --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No. Transaksi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dari / Kepada</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Staff</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse ($transactions as $transaction)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-slate-700">
                                        {{ $transaction->transaction_number }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php $isIncoming = $transaction->type === 'incoming'; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isIncoming ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-orange-50 text-orange-700 border border-orange-100' }}">
                                            @if($isIncoming) <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg> @else <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> @endif
                                            {{ $isIncoming ? 'Masuk' : 'Keluar' }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                        @if ($transaction->type === 'incoming')
                                            <div class="text-xs text-slate-400">Supplier</div>
                                            <div class="font-medium">{{ $transaction->supplier->name ?? 'Dihapus' }}</div>
                                        @else
                                            <div class="text-xs text-slate-400">Customer</div>
                                            <div class="font-medium">{{ $transaction->customer_name ?? '-' }}</div>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                        {{ $transaction->user->name ?? 'User Dihapus' }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusClass = match ($transaction->status) {
                                                'Approved', 'Verified', 'Shipped' => 'bg-emerald-100 text-emerald-800',
                                                'Pending' => 'bg-amber-100 text-amber-800',
                                                'Rejected' => 'bg-rose-100 text-rose-800',
                                                default => 'bg-slate-100 text-slate-800',
                                            };
                                        @endphp
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full {{ $statusClass }}">
                                            {{ $transaction->status }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                        <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-800 font-bold hover:underline">
                                            Detail
                                        </a>
                                        
                                        {{-- Indikator jika butuh approval (untuk Manager) --}}
                                        @if (Auth::user()->isManager() && $transaction->status === 'Pending')
                                            <div class="mt-1 flex justify-center">
                                                <span class="flex h-2 w-2 relative">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                            <p>Belum ada data transaksi.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="bg-slate-50 px-4 py-3 border-t border-slate-200 sm:px-6">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>