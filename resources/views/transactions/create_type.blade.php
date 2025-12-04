<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaksi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Pilih Jenis Transaksi') }}</h3>

                    <div class="flex flex-wrap justify-center gap-6">
                        
                        {{-- Opsi 1: Barang Masuk (Incoming) --}}
                        <a href="{{ route('transactions.create_incoming') }}" class="w-full sm:w-64 block p-6 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg shadow-md transition duration-200 group">
                            <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-blue-800">{{ __('Barang Masuk') }}</h4>
                            <p class="text-sm text-gray-600 mt-2">Catat stok yang diterima dari supplier atau hasil restock.</p>
                        </a>

                        {{-- Opsi 2: Barang Keluar (Outgoing) --}}
                        <a href="{{ route('transactions.create_outgoing') }}" class="w-full sm:w-64 block p-6 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg shadow-md transition duration-200 group">
                            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center group-hover:bg-red-200 transition">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 10H4m0 0l4-4m-4 4l4 4m12 6h-8m0 0l-4 4m4-4l-4-4"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-red-800">{{ __('Barang Keluar') }}</h4>
                            <p class="text-sm text-gray-600 mt-2">Catat barang yang dikirim ke customer atau keluar gudang.</p>
                        </a>

                    </div>
                    
                    <div class="mt-8 border-t pt-6">
                        <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            &larr; {{ __('Kembali ke Daftar') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>