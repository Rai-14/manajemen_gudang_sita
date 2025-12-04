<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Notifikasi Sukses --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Notifikasi Error --}}
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Header & Tombol Tambah --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('Daftar Semua Transaksi') }}</h3>
                        
                        {{-- Tombol Transaksi Baru (Hanya Staff) --}}
                        @if (Auth::user()->isStaff())
                            <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                {{ __('+ Transaksi Baru') }}
                            </a>
                        @endif
                    </div>

                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('transactions.index') }}" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex flex-wrap items-end gap-4">
                            {{-- Filter Tipe --}}
                            <div class="w-full sm:w-1/4">
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Filter Tipe</label>
                                <select name="type" id="type" onchange="this.form.submit()" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <option value="">Semua Tipe</option>
                                    <option value="incoming" @selected(request('type') == 'incoming')>Barang Masuk (Incoming)</option>
                                    <option value="outgoing" @selected(request('type') == 'outgoing')>Barang Keluar (Outgoing)</option>
                                </select>
                            </div>
                            
                            {{-- Filter Status --}}
                            <div class="w-full sm:w-1/4">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                                <select name="status" id="status" onchange="this.form.submit()" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <option value="">Semua Status</option>
                                    <option value="Pending" @selected(request('status') == 'Pending')>Pending</option>
                                    <option value="Verified" @selected(request('status') == 'Verified')>Verified (Masuk)</option>
                                    <option value="Approved" @selected(request('status') == 'Approved')>Approved (Keluar)</option>
                                    <option value="Rejected" @selected(request('status') == 'Rejected')>Rejected</option>
                                    {{-- Opsi khusus Manager --}}
                                    @if(Auth::user()->isManager() || Auth::user()->isAdmin())
                                        <option value="pending_approval" @selected(request('status') == 'pending_approval')>⚠️ Perlu Persetujuan Saya</option>
                                    @endif
                                </select>
                            </div>

                            {{-- Tombol Reset --}}
                            @if (request()->has('type') || request()->has('status'))
                                <div>
                                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                                        {{ __('Reset Filter') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>

                    {{-- Tabel Transaksi --}}
                    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dari / Kepada</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($transactions as $transaction)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            {{ $transaction->transaction_number }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $isIncoming = $transaction->type === 'incoming';
                                            @endphp
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isIncoming ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $isIncoming ? 'Barang Masuk' : 'Barang Keluar' }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($transaction->type === 'incoming')
                                                <span class="text-gray-500">Supplier:</span> <br>
                                                {{ $transaction->supplier->name ?? 'Supplier Dihapus' }}
                                            @else
                                                <span class="text-gray-500">Customer:</span> <br>
                                                {{ $transaction->customer_name ?? '-' }}
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->user->name ?? 'User Dihapus' }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $statusColor = match ($transaction->status) {
                                                    'Approved', 'Verified', 'Shipped' => 'bg-green-100 text-green-800',
                                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                                    'Rejected' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                {{ $transaction->status }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <a href="{{ route('transactions.show', $transaction) }}" class="text-indigo-600 hover:text-indigo-900 font-bold border border-indigo-200 px-3 py-1 rounded hover:bg-indigo-50">
                                                Detail
                                            </a>
                                            
                                            {{-- Indikator jika butuh approval (untuk Manager) --}}
                                            @if (Auth::user()->isManager() && $transaction->status === 'Pending')
                                                <div class="mt-1 text-xs text-red-500 font-bold animate-pulse">
                                                    Butuh Approval
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-10 whitespace-nowrap text-sm text-gray-500 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                                <p>{{ __('Belum ada data transaksi.') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>