<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Inventori Gudang') }}
            </h2>
            <a href="{{ route('reports.print') }}" target="_blank" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm font-bold flex items-center shadow">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- 1. RINGKASAN FINANSIAL --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500 font-medium">Total Item Fisik</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($totalItems) }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-indigo-500">
                    <p class="text-sm text-gray-500 font-medium">Total Nilai Aset</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">Berdasarkan Harga Beli</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-emerald-500">
                    <p class="text-sm text-gray-500 font-medium">Potensi Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPotentialRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">Berdasarkan Harga Jual</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-teal-500">
                    <p class="text-sm text-gray-500 font-medium">Estimasi Profit</p>
                    <p class="text-2xl font-bold text-teal-600">+ Rp {{ number_format($potentialProfit, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">Gross Profit</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- 2. BREAKDOWN PER KATEGORI --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Analisa Kategori</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 uppercase">
                                <tr>
                                    <th class="py-2 px-3">Kategori</th>
                                    <th class="py-2 px-3 text-center">Jml Item</th>
                                    <th class="py-2 px-3 text-right">Nilai Aset</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($categories as $cat)
                                <tr>
                                    <td class="py-3 px-3 font-medium">{{ $cat->name }}</td>
                                    <td class="py-3 px-3 text-center">{{ $cat->total_stock }}</td>
                                    <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($cat->asset_value, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 3. MOVING ITEMS --}}
                <div class="space-y-6">
                    {{-- Stok Terbanyak --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 text-blue-600">Stok Terbanyak (Top 5)</h3>
                        <ul class="space-y-3">
                            @foreach($topProducts as $prod)
                            <li class="flex justify-between items-center border-b border-gray-50 pb-2">
                                <span class="text-gray-700">{{ $prod->name }}</span>
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded-full">{{ $prod->current_stock }} {{ $prod->unit }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Stok Paling Sedikit --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 text-orange-600">Stok Menipis (Bottom 5)</h3>
                        <ul class="space-y-3">
                            @foreach($lowProducts as $prod)
                            <li class="flex justify-between items-center border-b border-gray-50 pb-2">
                                <span class="text-gray-700">{{ $prod->name }}</span>
                                <span class="bg-orange-100 text-orange-800 text-xs font-bold px-2 py-1 rounded-full">{{ $prod->current_stock }} {{ $prod->unit }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>