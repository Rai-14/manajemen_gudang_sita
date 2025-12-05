<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <div class="p-2 bg-rose-100 rounded-lg text-rose-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
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
                    <strong class="font-bold block">Validasi Gagal!</strong>
                    <span class="text-sm">Mohon periksa stok tersedia atau data yang belum lengkap.</span>
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
                    
                    {{-- KOLOM KIRI: INFO HEADER --}}
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                            <h3 class="text-slate-800 font-bold text-lg mb-4 border-b border-slate-100 pb-2">Info Pengiriman</h3>
                            
                            <div class="space-y-4">
                                {{-- Nomor Transaksi --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor Referensi</label>
                                    <div class="flex items-center bg-slate-100 rounded-lg px-3 py-2 border border-slate-200 text-slate-500 font-mono text-sm">
                                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                        AUTO GENERATED
                                    </div>
                                </div>

                                {{-- Tanggal --}}
                                <div>
                                    <label for="transaction_date" class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Pengiriman</label>
                                    <input id="transaction_date" type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                                        class="block w-full rounded-lg border-slate-300 bg-white text-sm focus:border-rose-500 focus:ring-rose-500 shadow-sm">
                                </div>

                                {{-- Customer --}}
                                <div>
                                    <label for="customer_name" class="block text-xs font-bold text-slate-500 uppercase mb-1">Customer / Tujuan</label>
                                    <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name') }}" required placeholder="PT. Tujuan Jaya"
                                        class="block w-full rounded-lg border-slate-300 bg-white text-sm focus:border-rose-500 focus:ring-rose-500 shadow-sm">
                                </div>

                                {{-- Catatan --}}
                                <div>
                                    <label for="notes" class="block text-xs font-bold text-slate-500 uppercase mb-1">Catatan</label>
                                    <textarea id="notes" name="notes" rows="4" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-sm focus:border-rose-500 focus:ring-rose-500 shadow-sm placeholder-slate-400" placeholder="Keterangan pengiriman...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Tombol Mobile --}}
                        <div class="block lg:hidden">
                            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Pengiriman
                            </button>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: ITEM LIST --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-6 border-b border-slate-100 bg-rose-50/30 flex justify-between items-center">
                                <h3 class="text-slate-800 font-bold text-lg">Daftar Barang Keluar</h3>
                                <button type="button" @click="addRow()" class="text-xs font-bold bg-rose-100 text-rose-700 px-3 py-1.5 rounded-lg hover:bg-rose-200 transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah Baris
                                </button>
                            </div>

                            <div class="p-0 overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/3">Produk</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/5">Stok Tersedia</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/5">Jumlah Keluar</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/6">Satuan</th>
                                            <th class="px-4 py-3 w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-200">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="px-4 py-3">
                                                    <select :name="`products[${index}][product_id]`" 
                                                            x-model="item.product_id" 
                                                            @change="updateInfo(index)"
                                                            required 
                                                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500 shadow-sm">
                                                        <option value="">-- Pilih Produk --</option>
                                                        <template x-for="prod in products" :key="prod.id">
                                                            <option :value="prod.id" x-text="`${prod.name} (SKU: ${prod.sku})`"></option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3">
                                                    {{-- Indikator Stok --}}
                                                    <div class="flex items-center gap-2 bg-slate-100 border border-slate-200 rounded-lg px-3 py-2">
                                                        <span class="font-mono font-bold text-slate-700" x-text="item.stock"></span>
                                                        <span class="text-xs text-slate-400" x-show="item.stock > 0">(Available)</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" 
                                                           :name="`products[${index}][quantity]`" 
                                                           x-model="item.quantity" 
                                                           min="1" 
                                                           :max="item.stock"
                                                           required
                                                           class="block w-full rounded-lg border-slate-300 text-sm focus:border-rose-500 focus:ring-rose-500 shadow-sm text-center font-bold"
                                                           :class="{'border-rose-500 bg-rose-50 text-rose-700': item.quantity > item.stock}" 
                                                           placeholder="0" />
                                                    
                                                    <div x-show="parseInt(item.quantity) > item.stock" class="text-[10px] text-rose-600 mt-1 font-bold">
                                                        Melebihi stok!
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="text" readonly :value="item.unit || '-'" class="block w-full rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-500 text-center">
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" @click="removeRow(index)" class="text-slate-400 hover:text-rose-500 transition p-2 rounded-full hover:bg-rose-50" :disabled="items.length === 1" title="Hapus Baris">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="p-6 bg-slate-50 border-t border-slate-200">
                                <div class="flex items-center justify-between">
                                    <button type="button" @click="addRow()" class="text-sm text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Tambah Baris Lain
                                    </button>
                                    
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('transactions.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm">Batal</a>
                                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Simpan Pengiriman
                                        </button>
                                    </div>
                                </div>
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
                items: [],

                init() {
                    const oldInput = @json(old('products'));
                    if (oldInput && Object.keys(oldInput).length > 0) {
                        this.items = Object.values(oldInput).map(item => {
                            const product = this.products.find(p => p.id == item.product_id);
                            return { 
                                product_id: item.product_id, 
                                quantity: item.quantity, 
                                stock: product ? product.current_stock : 0, 
                                unit: product ? product.unit : '' 
                            };
                        });
                    } else {
                        this.addRow();
                    }
                },
                
                addRow() {
                    this.items.push({ product_id: '', quantity: '', stock: 0, unit: '' });
                },

                removeRow(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                
                updateInfo(index) {
                    const selectedId = this.items[index].product_id;
                    const product = this.products.find(p => p.id == selectedId);
                    
                    if (product) {
                        this.items[index].stock = product.current_stock;
                        this.items[index].unit = product.unit;
                    } else {
                        this.items[index].stock = 0;
                        this.items[index].unit = '';
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>