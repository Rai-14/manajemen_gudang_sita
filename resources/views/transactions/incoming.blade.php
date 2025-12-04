<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Tampilkan Error Validasi Global --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Periksa Inputan!</strong>
                    <span class="block sm:inline">Mohon cek kembali data yang Anda masukkan.</span>
                </div>
            @endif

            {{-- Tampilkan Error System/Session --}}
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="transactionForm()">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">{{ __('Detail Transaksi: Barang Masuk') }}</h3>

                    <form method="POST" action="{{ route('transactions.store_incoming') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Kolom Kiri: Info Utama --}}
                            <div>
                                <div class="mb-4">
                                    <label class="block font-medium text-sm text-gray-700">Nomor Transaksi</label>
                                    <input type="text" value="OTOMATIS (AUTO)" disabled class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="transaction_date" class="block font-medium text-sm text-gray-700">Tanggal Penerimaan</label>
                                    <input id="transaction_date" type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @error('transaction_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="supplier_id" class="block font-medium text-sm text-gray-700">Supplier</label>
                                    <select id="supplier_id" name="supplier_id" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Kolom Kanan: Catatan --}}
                            <div>
                                <div class="mb-4">
                                    <label for="notes" class="block font-medium text-sm text-gray-700">Catatan / Referensi</label>
                                    <textarea id="notes" name="notes" rows="6" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="text-lg font-medium text-gray-900 mt-8 mb-4 border-t pt-4">{{ __('Daftar Produk') }}</h4>

                        {{-- Tabel Dinamis dengan Alpine Loop --}}
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-1/2">Nama Produk</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-1/4">Jumlah (Qty)</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-1/4">Satuan</th>
                                        <th class="px-3 py-2 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="p-2">
                                                <select :name="`products[${index}][product_id]`" 
                                                        x-model="item.product_id" 
                                                        @change="updateUnit(index)"
                                                        required 
                                                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="">-- Pilih Produk --</option>
                                                    <template x-for="prod in products" :key="prod.id">
                                                        <option :value="prod.id" x-text="`${prod.name} (SKU: ${prod.sku})`"></option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td class="p-2">
                                                <input type="number" 
                                                       :name="`products[${index}][quantity]`" 
                                                       x-model="item.quantity" 
                                                       min="1" required
                                                       class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                                       placeholder="0" />
                                            </td>
                                            <td class="p-2">
                                                <span x-text="item.unit || '-'" class="text-sm text-gray-600 block py-2 px-2 bg-gray-50 rounded border border-gray-200"></span>
                                                <input type="hidden" :name="`products[${index}][unit]`" x-model="item.unit">
                                            </td>
                                            <td class="p-2 text-center">
                                                <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-700 p-2" :disabled="items.length === 1" title="Hapus Baris">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-start mb-8">
                             <button type="button" @click="addRow()" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Item Produk
                            </button>
                        </div>

                        <div class="flex items-center justify-end border-t pt-4">
                            <a href="{{ route('transactions.index') }}" class="mr-4 text-sm font-semibold text-gray-600 hover:text-gray-900">Batal</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Simpan Transaksi Masuk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('transactionForm', () => ({
                products: @json($products),
                items: [],

                init() {
                    // Ambil data old() dari Laravel jika validasi gagal agar tidak hilang
                    const oldInput = @json(old('products'));
                    
                    if (oldInput && Object.keys(oldInput).length > 0) {
                        // Mapping old input kembali ke format items Alpine
                        this.items = Object.values(oldInput).map(item => {
                            const product = this.products.find(p => p.id == item.product_id);
                            return { 
                                product_id: item.product_id, 
                                quantity: item.quantity, 
                                unit: product ? product.unit : '' 
                            };
                        });
                    } else {
                        // Default 1 baris kosong saat pertama kali buka
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