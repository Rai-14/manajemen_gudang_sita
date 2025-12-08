<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Data Supplier (PT/CV)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-end mb-4">
                <a href="{{ route('suppliers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Tambah Supplier Baru
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-100">
                                <th class="p-3">Nama Perusahaan</th>
                                <th class="p-3">Email</th> <!-- Tambah Kolom -->
                                <th class="p-3">Telepon</th>
                                <th class="p-3">Alamat</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 font-bold">{{ $supplier->name }}</td>
                                <td class="p-3 text-blue-600">{{ $supplier->email }}</td> <!-- Tampilkan Email -->
                                <td class="p-3">{{ $supplier->phone ?? '-' }}</td>
                                <td class="p-3 text-sm text-gray-500">{{ Str::limit($supplier->address, 30) }}</td>
                                <td class="p-3 text-center flex justify-center gap-2">
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-yellow-500 hover:text-yellow-700 font-semibold">Edit</a>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Hapus supplier ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $suppliers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>