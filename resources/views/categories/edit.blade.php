<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kategori') }}: {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Perbarui Informasi Kategori') }}</h3>

                    {{-- Form Update --}}
                    <form method="POST" action="{{ route('categories.update', $category) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Nama Kategori --}}
                        <div class="mb-4">
                            <label for="name" class="block font-medium text-sm text-gray-700">Nama Kategori</label>
                            <input id="name" type="text" name="name" 
                                value="{{ old('name', $category->name) }}" required autofocus
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Deskripsi --}}
                        <div class="mb-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi (Opsional)</label>
                            <textarea id="description" name="description" rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('description') border-red-500 @enderror">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Gambar Kategori --}}
                        <div class="mb-6">
                            <label for="image_path" class="block font-medium text-sm text-gray-700">Gambar Kategori (Biarkan kosong jika tidak ingin mengubah)</label>
                            <input id="image_path" type="file" name="image_path"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            
                            {{-- Preview Gambar Lama --}}
                            @if ($category->image_path)
                                <div class="mt-2 flex items-center">
                                    <span class="text-xs text-gray-500 mr-2">Gambar Saat Ini:</span>
                                    <a href="{{ Storage::url($category->image_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-xs underline">
                                        Lihat Gambar
                                    </a>
                                </div>
                            @endif

                            @error('image_path')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end border-t pt-4">
                            <a href="{{ route('categories.index') }}" class="mr-4 text-sm font-semibold text-gray-600 hover:text-gray-900">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Simpan Perubahan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>