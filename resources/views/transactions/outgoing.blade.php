<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Pengiriman Barang (Outbound)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Error Validasi --}}
            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r shadow-sm">
                    <strong class="font-bold">Periksa Inputan!</strong>
                    <ul class="list-disc list-inside text-sm mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('transactions.store_outgoing') }}" x-data="transactionForm()">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- INFO HEADER --}}
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                            <h3 class="text-slate-800 font-bold text-lg mb-4 border-b border-slate-100 pb-2">Info Pengiriman</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Pengiriman</label>
                                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Customer / Tujuan</label>
                                    <input type="text" name="customer_name" required placeholder="Contoh: PT. Maju Jaya"
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Catatan</label>
                                    <textarea name="notes" rows="4" class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Keterangan pengiriman..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Tombol Submit Mobile --}}
                        <div class="block lg:hidden">
                            <button type="submit" class="w-full bg-slate-800 text-white font-bold py-3 px-4 rounded-xl">Simpan Transaksi</button>
                        </div>
                    </div>

                    {{-- ITEM LIST --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between">
                                <h3 class="text-slate-800 font-bold text-lg">Daftar Barang Keluar</h3>
                                <button type="button" @click="addRow()" class="text-xs font-bold bg-orange-100 text-orange-700 px-3 py-1.5 rounded-lg hover:bg-orange-200">
                                    + Tambah Baris
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-1/2">Produk</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-1/4">Qty</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-1/6">Stok</th>
                                            <th class="w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-200">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <select :name="`products[${index}][product_id]`" x-model="item.product_id" @change="updateStock(index)" required class="block w-full rounded-lg border-slate-300 text-sm">
                                                        <option value="">-- Pilih Produk --</option>
                                                        <template x-for="prod in products" :key="prod.id">
                                                            <option :value="prod.id" x-text="`${prod.name}`"></option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" :name="`products[${index}][quantity]`" x-model="item.quantity" min="1" :max="item.stock" required class="block w-full rounded-lg border-slate-300 text-sm text-center">
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm">
                                                    <span x-text="item.stock || '-'" :class="{'text-red-600 font-bold': item.quantity > item.stock}"></span>
                                                    <span x-text="item.unit" class="text-xs text-gray-500"></span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" @click="removeRow(index)" class="text-slate-400 hover:text-red-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="p-6 bg-slate-50 flex justify-end gap-4">
                                <a href="{{ route('transactions.index') }}" class="text-slate-500 font-medium text-sm py-2">Batal</a>
                                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md">
                                    Simpan Transaksi
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('transactionForm', () => ({
                products: @json($products),
                items: [{ product_id: '', quantity: '', stock: 0, unit: '' }],

                addRow() { this.items.push({ product_id: '', quantity: '', stock: 0, unit: '' }); },
                removeRow(index) { if(this.items.length > 1) this.items.splice(index, 1); },
                
                updateStock(index) {
                    const selectedId = this.items[index].product_id;
                    const product = this.products.find(p => p.id == selectedId);
                    if(product) {
                        // PERBAIKAN: Menggunakan 'current_stock' sesuai Controller
                        this.items[index].stock = product.current_stock; 
                        this.items[index].unit = product.unit;
                    } else {
                        this.items[index].stock = 0;
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>