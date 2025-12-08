<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                Edit Transaksi Masuk: <span class="font-mono text-blue-600">{{ $transaction->transaction_number }}</span>
            </h2>
        </div>
    </x-slot>

    {{-- 1. PHP BLOCK: Format Data agar bersih --}}
    @php
        $detailItems = $transaction->details->map(function($detail) {
            return [
                'product_id' => $detail->product_id, // Biarkan raw, kita handle di JS
                'quantity' => $detail->quantity,
                'unit' => $detail->product->unit ?? '',
                'current_stock' => $detail->product->current_stock ?? 0
            ];
        })->values();
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
                    
                    {{-- KOLOM KIRI: Info Utama --}}
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                            <h3 class="text-slate-800 font-bold text-lg mb-4 border-b border-slate-100 pb-2">Info Transaksi</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Penerimaan</label>
                                    <input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date) }}" required
                                           class="block w-full rounded-lg border-slate-300 bg-white text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Supplier</label>
                                    <select name="supplier_id" required class="block w-full rounded-lg border-slate-300 bg-white text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $transaction->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Catatan</label>
                                    <textarea name="notes" rows="4" class="block w-full rounded-lg border-slate-300 bg-slate-50 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">{{ old('notes', $transaction->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="block lg:hidden">
                            <button type="submit" class="w-full bg-slate-800 text-white font-bold py-3 px-4 rounded-xl">Update Transaksi</button>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: Daftar Barang --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between">
                                <h3 class="text-slate-800 font-bold text-lg">Daftar Barang</h3>
                                <button type="button" @click="addRow()" class="text-xs font-bold bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-200">
                                    + Tambah Baris
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-1/2">Produk</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-1/4">Qty</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase w-1/6">Satuan</th>
                                            <th class="w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-200">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr>
                                                <td class="px-4 py-3">
                                                    {{-- Perbaikan 1: Gunakan x-model.number untuk memaksa tipe data angka --}}
                                                    <select :name="'products['+index+'][product_id]'" 
                                                            x-model.number="item.product_id" 
                                                            @change="updateUnit(index)" 
                                                            required 
                                                            class="block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                                        <option value="">-- Pilih Produk --</option>
                                                        <template x-for="prod in products" :key="prod.id">
                                                            <option :value="prod.id" 
                                                                    :selected="item.product_id === prod.id"
                                                                    x-text="prod.name + ' (Stok: ' + prod.current_stock + ')'">
                                                            </option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" :name="'products['+index+'][quantity]'" x-model="item.quantity" min="1" required class="block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm text-center">
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm">
                                                    <span x-text="item.unit" class="text-gray-500 font-medium"></span>
                                                    <input type="hidden" :name="'products['+index+'][unit]'" x-model="item.unit">
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" @click="removeRow(index)" class="text-slate-400 hover:text-red-500 transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="p-6 bg-slate-50 flex justify-end gap-4">
                                <a href="{{ route('transactions.index') }}" class="text-slate-500 font-medium text-sm py-2 hover:text-slate-700">Batal</a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition">
                                    Simpan Perubahan
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
        // Perbaikan 2: Definisikan data di luar Alpine Object untuk menghindari Syntax Error
        const _productsData = @json($products);
        const _currentItems = @json($detailItems);
        const _oldInputs = @json(old('products', []));

        document.addEventListener('alpine:init', () => {
            Alpine.data('transactionForm', () => ({
                products: _productsData,
                items: [], 

                init() {
                    // Skenario 1: Jika ada Old Input (Validasi Gagal)
                    if (_oldInputs && Object.keys(_oldInputs).length > 0) {
                        this.items = Object.values(_oldInputs).map(item => {
                            const product = this.products.find(p => p.id == item.product_id);
                            return { 
                                product_id: parseInt(item.product_id), 
                                quantity: parseInt(item.quantity), 
                                unit: product ? product.unit : '' 
                            };
                        });
                    } 
                    // Skenario 2: Mode Edit (Data Database)
                    else if (_currentItems && _currentItems.length > 0) {
                        this.items = _currentItems.map(item => ({
                            product_id: parseInt(item.product_id), // Paksa jadi Integer agar Dropdown terpilih
                            quantity: parseInt(item.quantity),
                            unit: item.unit
                        }));
                    }
                    // Skenario 3: Data Kosong (Buat baru)
                    else {
                        this.addRow();
                    }
                },

                addRow() { 
                    this.items.push({ product_id: '', quantity: 1, unit: '' }); 
                },
                
                removeRow(index) { 
                    if(this.items.length > 1) this.items.splice(index, 1); 
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