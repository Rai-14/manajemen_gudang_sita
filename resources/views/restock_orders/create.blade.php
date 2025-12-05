<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Buat Purchase Order (PO)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- AlpineJS Wrapper --}}
            <div x-data="restockForm()">
                
                <form method="POST" action="{{ route('restock_orders.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        {{-- KIRI: HEADER PO --}}
                        <div class="lg:col-span-1 space-y-6">
                            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                                <h3 class="text-slate-800 font-bold text-lg mb-4 border-b border-slate-100 pb-2">Informasi Pesanan</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor PO</label>
                                        <div class="flex items-center bg-slate-100 rounded-lg px-3 py-2 border border-slate-200 text-slate-500 font-mono text-sm">
                                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            AUTO GENERATED
                                        </div>
                                    </div>

                                    <div>
                                        <label for="supplier_id" class="block text-xs font-bold text-slate-500 uppercase mb-1">Supplier Tujuan</label>
                                        <select id="supplier_id" name="supplier_id" required class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                                            <option value="">-- Pilih Supplier --</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="order_date" class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Order</label>
                                        <input id="order_date" type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                                        @error('order_date') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="expected_delivery_date" class="block text-xs font-bold text-slate-500 uppercase mb-1">Estimasi Tiba (Opsional)</label>
                                        <input id="expected_delivery_date" type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                                    </div>

                                    <div>
                                        <label for="notes" class="block text-xs font-bold text-slate-500 uppercase mb-1">Catatan untuk Supplier</label>
                                        <textarea id="notes" name="notes" rows="3" class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm placeholder-slate-400" placeholder="Instruksi khusus...">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Tombol Simpan Mobile --}}
                            <div class="block lg:hidden">
                                <button type="submit" class="w-full bg-orange-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg flex justify-center items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    Kirim PO
                                </button>
                            </div>
                        </div>

                        {{-- KANAN: DAFTAR BARANG --}}
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                                <div class="p-6 border-b border-slate-100 bg-orange-50/30 flex justify-between items-center">
                                    <h3 class="text-slate-800 font-bold text-lg">Daftar Barang</h3>
                                    <button type="button" @click="addRow()" class="text-xs font-bold bg-orange-100 text-orange-700 px-3 py-1.5 rounded-lg hover:bg-orange-200 transition flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Tambah Item
                                    </button>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/2">Produk</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/4">Stok Saat Ini</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/4">Jml Order</th>
                                                <th class="px-4 py-3 w-10"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-slate-200">
                                            <template x-for="(item, index) in items" :key="index">
                                                <tr class="hover:bg-slate-50 transition">
                                                    <td class="px-4 py-3">
                                                        <select :name="`products[${index}][product_id]`" 
                                                                x-model="item.product_id" 
                                                                @change="updateProductInfo(index)"
                                                                required 
                                                                class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                                                            <option value="">-- Pilih Produk --</option>
                                                            <template x-for="prod in products" :key="prod.id">
                                                                <option :value="prod.id" x-text="`${prod.name} (SKU: ${prod.sku})`"></option>
                                                            </template>
                                                        </select>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="bg-slate-100 rounded px-2 py-1.5 text-sm text-slate-600 font-mono border border-slate-200" x-text="`${item.current_stock} ${item.unit}`"></div>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <input type="number" :name="`products[${index}][quantity]`" x-model="item.quantity" min="1" required class="block w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm" placeholder="0">
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <button type="button" @click="removeRow(index)" class="text-slate-400 hover:text-rose-500 p-2 rounded-full hover:bg-rose-50 transition" :disabled="items.length === 1" title="Hapus Baris">
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
                                        <button type="button" @click="addRow()" class="text-sm text-orange-600 hover:text-orange-800 font-bold flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Tambah Baris Lain
                                        </button>
                                        
                                        <div class="flex items-center gap-4">
                                            <a href="{{ route('restock_orders.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm">Batal</a>
                                            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                                Kirim PO
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
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('restockForm', () => ({
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
                                current_stock: product ? product.current_stock : 0, 
                                unit: product ? product.unit : '' 
                            };
                        });
                    } else {
                        this.addRow();
                    }
                },
                
                addRow() {
                    this.items.push({ product_id: '', quantity: '', current_stock: 0, unit: '' });
                },

                removeRow(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                
                updateProductInfo(index) {
                    const selectedId = this.items[index].product_id;
                    const product = this.products.find(p => p.id == selectedId);
                    this.items[index].current_stock = product ? product.current_stock : 0;
                    this.items[index].unit = product ? product.unit : '';
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>