<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header & Tombol Tambah -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h2>
                <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
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
                                <th class="p-4 font-semibold text-gray-700">Status</th> <!-- Kolom Baru -->
                                <th class="p-4 font-semibold text-gray-700 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($users as $user)
                            {{-- Baris kuning jika pending --}}
                            <tr class="hover:bg-gray-50 transition {{ $user->status == 'pending' ? 'bg-yellow-50' : '' }}">
                                <td class="p-4">
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                </td>
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
                                <td class="p-4">
                                    @if($user->status == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                                            Menunggu Approval
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        
                                        {{-- TOMBOL APPROVE (Hanya Muncul Jika Pending) --}}
                                        @if($user->status == 'pending')
                                            <form action="{{ route('users.approve', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-green-600 text-white text-xs px-3 py-1.5 rounded hover:bg-green-700 shadow-sm transition" onclick="return confirm('Setujui user ini agar bisa login?')">
                                                    ✅ Setujui
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-800 font-medium text-sm">Edit</a>
                                        
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="p-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>