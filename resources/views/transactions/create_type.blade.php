@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('New Transaction') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Select Transaction Type') }}</h3>

                    <div class="flex flex-wrap justify-center gap-6">
                        
                        {{-- Opsi 1: Barang Masuk (Incoming) --}}
                        <a href="{{ route('transactions.create_incoming') }}" class="w-full sm:w-64 block p-6 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg shadow-md transition duration-200">
                            <svg class="w-10 h-10 mx-auto mb-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            <h4 class="text-lg font-semibold text-blue-800">{{ __('Incoming Stock') }}</h4>
                            <p class="text-sm text-gray-600 mt-1">Record items received from suppliers or restock orders.</p>
                        </a>

                        {{-- Opsi 2: Barang Keluar (Outgoing) --}}
                        <a href="{{ route('transactions.create_outgoing') }}" class="w-full sm:w-64 block p-6 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg shadow-md transition duration-200">
                            <svg class="w-10 h-10 mx-auto mb-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 10H4m0 0l4-4m-4 4l4 4m12 6h-8m0 0l-4 4m4-4l-4-4"></path></svg>
                            <h4 class="text-lg font-semibold text-red-800">{{ __('Outgoing Stock') }}</h4>
                            <p class="text-sm text-gray-600 mt-1">Record items shipped out to customers or other departments.</p>
                        </a>

                    </div>
                    
                    <div class="mt-8">
                        <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection