<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Area Kerja Staff') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-800">Halo, {{ Auth::user()->name }}!</h3>
                <p class="text-gray-600">Anda telah mencatat <strong>{{ $transactionsToday }}</strong> transaksi hari ini. Semangat bekerja!</p>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Tombol Barang Masuk -->
                <a href="{{ route('transactions.create_incoming') }}" class="group block bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition hover:border-blue-400">
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl group-hover:bg-blue-600 group-hover:text-white transition">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xl font-bold text-gray-800">Barang Masuk</h4>
                            <p class="text-gray-500 text-sm">Catat penerimaan barang dari Supplier</p>
                        </div>
                    </div>
                </a>

                <!-- Tombol Barang Keluar -->
                <a href="{{ route('transactions.create_outgoing') }}" class="group block bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition hover:border-orange-400">
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 text-2xl group-hover:bg-orange-600 group-hover:text-white transition">
                            <i class="fas fa-truck-moving"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xl font-bold text-gray-800">Barang Keluar</h4>
                            <p class="text-gray-500 text-sm">Catat pengiriman barang ke Customer</p>
                        </div>
                    </div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>