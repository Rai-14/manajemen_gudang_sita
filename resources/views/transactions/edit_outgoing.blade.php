<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                Edit Transaksi Keluar: <span class="font-mono text-orange-600">{{ $transaction->transaction_number }}</span>
            </h2>
        </div>
    </x-slot>

    {{-- PERBAIKAN: Siapkan data di PHP dulu agar @json tidak bingung --}}
    @php
        $detailItems = $transaction->details->map(function($detail) {
            return [
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'unit' => $detail->product->unit ?? '',
                'current_stock' => $detail->product->current_stock ?? 0
            ];
        });
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
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

            <form method="POST" action="{{ route('transactions.update', $transaction->id) }}" x-data="transactionForm()">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- KOLOM KIRI --}}
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                            <h3 class="text-slate-800 font-bold text-lg mb-4 border-b border-slate-100 pb-2">Info Pengiriman</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Pengiriman</label>
                                    <input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date) }}" required
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Customer</label>
                                    <input type="text" name="customer_name" value="{{ old('customer_name', $transaction->customer_name) }}" required
                                           class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Catatan</label>
                                    <textarea name="notes" rows="4" class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('notes', $transaction->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="block lg:hidden">
                            <button type="submit" class="w-full bg-slate-800 text-white font-bold py-3 px-4 rounded-xl">Update Transaksi</button>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
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
                                                    <select :name="'products['+index+'][product_id]'" x-model="item.product_id" @change="updateStock(index)" required class="block w-full rounded-lg border-slate-300 text-sm">
                                                        <option value="">-- Pilih Produk --</option>
                                                        <template x-for="prod in products" :key="prod.id">
                                                            <option :value="prod.id" x-text="prod.name"></option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" :name="'products['+index+'][quantity]'" x-model="item.quantity" min="1" :max="item.current_stock" required class="block w-full rounded-lg border-slate-300 text-sm text-center">
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm">
                                                    <span x-text="item.current_stock || '-'" :class="{'text-red-600 font-bold': item.quantity > item.current_stock}"></span>
                                                    <span x-text="item.unit" class="text-xs text-gray-500"></span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" @click="removeRow(index)" class="text-slate-400 hover:text-red-500">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
                                    Update Transaksi
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
                // Gunakan variabel PHP yang sudah disiapkan di atas
                items: @json($detailItems),

                init() {
                    const oldInput = @json(old('products'));
                    if (oldInput && Object.keys(oldInput).length > 0) {
                        this.items = Object.values(oldInput).map(item => {
                            const product = this.products.find(p => p.id == item.product_id);
                            return {
                                product_id: item.product_id,
                                quantity: item.quantity,
                                current_stock: product ? product.current_stock : 0,
                                unit: product ? product.unit : ''
                            };
                        });
                    }
                    if (this.items.length === 0) this.addRow();
                },

                addRow() { this.items.push({ product_id: '', quantity: '', current_stock: 0, unit: '' }); },
                removeRow(index) { if(this.items.length > 1) this.items.splice(index, 1); },
                
                updateStock(index) {
                    const selectedId = this.items[index].product_id;
                    const product = this.products.find(p => p.id == selectedId);
                    if(product) {
                        this.items[index].current_stock = product.current_stock;
                        this.items[index].unit = product.unit;
                    } else {
                        this.items[index].current_stock = 0;
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>