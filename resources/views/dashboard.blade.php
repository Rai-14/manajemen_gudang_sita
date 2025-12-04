<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Gudang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600">Sistem Manajemen Gudang & Inventaris.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-bold text-gray-800">📦 Transaksi</h4>
                            <span class="text-2xl">📋</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Kelola barang masuk dan keluar.</p>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('transactions.index') }}" class="text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                Riwayat Transaksi
                            </a>
                            <a href="{{ route('transactions.create') }}" class="text-center bg-white border border-blue-600 text-blue-600 hover:bg-blue-50 font-bold py-2 px-4 rounded transition">
                                + Transaksi Baru
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-bold text-gray-800">🏷️ Produk & Kategori</h4>
                            <span class="text-2xl">📦</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Data master barang dan stok.</p>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('products.index') }}" class="text-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                                Daftar Produk
                            </a>
                            <a href="{{ route('categories.index') }}" class="text-center bg-white border border-green-600 text-green-600 hover:bg-green-50 font-bold py-2 px-4 rounded transition">
                                Kategori
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-bold text-gray-800">🚚 Restock Order</h4>
                            <span class="text-2xl">📝</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Pesanan ke Supplier.</p>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('restock_orders.index') }}" class="text-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded transition">
                                Kelola Restock
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>