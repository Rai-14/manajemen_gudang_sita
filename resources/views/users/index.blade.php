<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header & Tombol Tambah -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h2>
                <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Tambah User Baru
                </a>
            </div>

            <!-- Pesan Sukses/Error -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Tabel Users -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="p-4 font-semibold text-gray-700">Nama</th>
                                <th class="p-4 font-semibold text-gray-700">Email</th>
                                <th class="p-4 font-semibold text-gray-700">Role</th>
                                <th class="p-4 font-semibold text-gray-700">Supplier Info</th>
                                <th class="p-4 font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4">{{ $user->name }}</td>
                                <td class="p-4">{{ $user->email }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 rounded text-xs font-bold uppercase
                                        {{ $user->role == 'admin' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $user->role == 'manager' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $user->role == 'staff' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $user->role == 'supplier' ? 'bg-green-100 text-green-700' : '' }}
                                    ">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="p-4 text-sm text-gray-500">
                                    {{ $user->supplier ? $user->supplier->name : '-' }}
                                </td>
                                <td class="p-4 flex gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                                    
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="p-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>