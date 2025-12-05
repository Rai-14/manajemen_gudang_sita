<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </div>
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Penerimaan Barang (Inbound)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Error Validasi --}}
            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r shadow-sm flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <strong class="font-bold block">Periksa Inputan!</strong>
                        <span class="text-sm">Mohon cek kembali data yang Anda masukkan.</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('transactions.store_incoming') }}" x-data="transactionForm()">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- KOLOM KIRI: INFO HEADER --}}
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                            <h3 class="text-slate-800 font-bold text-lg mb-4 border-b border-slate-100 pb-2">Info Transaksi</h3>
                            
                            <div class="space-y-4">
                                {{-- Nomor Transaksi --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor Referensi</label>
                                    <div class="flex items-center bg-slate-100 rounded-lg px-3 py-2 border border-slate-200 text-slate-500 font-mono text-sm">
                                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                        AUTO GENERATED
                                    </div>
                                </div>

                                {{-- Tanggal --}}
                                <div>
                                    <label for="transaction_date" class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Penerimaan</label>
                                    <input id="transaction_date" type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                                        class="block w-full rounded-lg border-slate-300 bg-white text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                </div>

                                {{-- Supplier --}}
                                <div>
                                    <label for="supplier_id" class="block text-xs font-bold text-slate-500 uppercase mb-1">Supplier</label>
                                    <select id="supplier_id" name="supplier_id" required class="block w-full rounded-lg border-slate-300 bg-white text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Catatan --}}
                                <div>
                                    <label for="notes" class="block text-xs font-bold text-slate-500 uppercase mb-1">Catatan / Referensi</label>
                                    <textarea id="notes" name="notes" rows="4" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm placeholder-slate-400" placeholder="Contoh: No. Surat Jalan, Kondisi barang, dll...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Tombol Aksi Mobile (Hidden di Desktop) --}}
                        <div class="block lg:hidden">
                            <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Transaksi
                            </button>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: ITEM LIST --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                                <h3 class="text-slate-800 font-bold text-lg">Daftar Barang Masuk</h3>
                                <button type="button" @click="addRow()" class="text-xs font-bold bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-200 transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah Baris
                                </button>
                            </div>

                            <div class="p-0 overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/2">Produk</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/4">Qty</th>
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
                                                            @change="updateUnit(index)"
                                                            required 
                                                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                                        <option value="">-- Pilih Produk --</option>
                                                        <template x-for="prod in products" :key="prod.id">
                                                            <option :value="prod.id" x-text="`${prod.name} (SKU: ${prod.sku})`"></option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" 
                                                           :name="`products[${index}][quantity]`" 
                                                           x-model="item.quantity" 
                                                           min="1" required
                                                           class="block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm text-center" 
                                                           placeholder="0" />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="text" readonly :value="item.unit || '-'" class="block w-full rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-500 text-center">
                                                    <input type="hidden" :name="`products[${index}][unit]`" x-model="item.unit">
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
                                    <button type="button" @click="addRow()" class="text-sm text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Tambah Baris Lain
                                    </button>
                                    
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('transactions.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm">Batal</a>
                                        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Simpan Transaksi
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
                                unit: product ? product.unit : '' 
                            };
                        });
                    } else {
                        this.addRow();
                    }
                },
                
                addRow() {
                    this.items.push({ product_id: '', quantity: '', unit: '' });
                },

                removeRow(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                
                updateUnit(index) {
                    const selectedId = this.items[index].product_id;
                    const product = this.products.find(p => p.id == selectedId);
                    this.items[index].unit = product ? product.unit : '';
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>