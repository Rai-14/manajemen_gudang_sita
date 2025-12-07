<x-app-layout>

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">

            {{ __('Portal Supplier') }}

        </h2>

    </x-slot>



    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

           

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold text-gray-800 mb-4">Pesanan Masuk (Restock Request)</h3>

               

                @if($orders->isEmpty())

                    <div class="text-center py-10 text-gray-500 bg-gray-50 rounded-lg">

                        <i class="fas fa-check-circle text-4xl mb-3 text-green-500"></i>

                        <p>Tidak ada pesanan baru saat ini.</p>

                    </div>

                @else

                    <div class="overflow-x-auto">

                        <table class="w-full text-left border-collapse">

                            <thead>

                                <tr class="bg-gray-100 border-b">

                                    <th class="p-3">Tanggal</th>

                                    <th class="p-3">ID Order</th>

                                    <th class="p-3">Status</th>

                                    <th class="p-3">Aksi</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($orders as $order)

                                <tr class="border-b hover:bg-gray-50">

                                    <td class="p-3">{{ $order->created_at->format('d M Y') }}</td>

                                    <td class="p-3 font-mono text-blue-600">#{{ $order->id }}</td>

                                    <td class="p-3">

                                        <span class="px-2 py-1 rounded text-xs font-bold uppercase

                                            {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}

                                            {{ $order->status == 'shipped' ? 'bg-blue-100 text-blue-700' : '' }}

                                            {{ $order->status == 'received' ? 'bg-green-100 text-green-700' : '' }}">

                                            {{ $order->status }}

                                        </span>

                                    </td>

                                    <td class="p-3">

                                        <!-- Tombol untuk update status pengiriman -->

                                        @if($order->status == 'pending')

                                            <form action="{{ route('restock_orders.update_status', $order->id) }}" method="POST">

                                                @csrf

                                                @method('PATCH')

                                                <input type="hidden" name="status" value="shipped">

                                                <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">

                                                    Kirim Barang

                                                </button>

                                            </form>

                                        @else

                                            <span class="text-gray-400 text-sm">Menunggu Gudang</span>

                                        @endif

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @endif

            </div>



        </div>

    </div>

</x-app-layout>