<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Restock Order (PO)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="restockForm()">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">{{ __('Detail Pesanan Pembelian') }}</h3>

                    <form method="POST" action="{{ route('restock_orders.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Kolom Kiri --}}
                            <div>
                                <div class="mb-4">
                                    <label class="block font-medium text-sm text-gray-700">Nomor PO (Otomatis)</label>
                                    <input type="text" value="AUTO GENERATED" disabled class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="supplier_id" class="block font-medium text-sm text-gray-700">Supplier Tujuan</label>
                                    <select id="supplier_id" name="supplier_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
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

                            {{-- Kolom Kanan --}}
                            <div>
                                <div class="mb-4">
                                    <label for="order_date" class="block font-medium text-sm text-gray-700">Tanggal Order</label>
                                    <input id="order_date" type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @error('order_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="expected_delivery_date" class="block font-medium text-sm text-gray-700">Estimasi Tiba (Opsional)</label>
                                    <input id="expected_delivery_date" type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>

                                <div class="mb-4">
                                    <label for="notes" class="block font-medium text-sm text-gray-700">Catatan</label>
                                    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <h4 class="text-lg font-medium text-gray-900 mt-8 mb-4 border-t pt-4">{{ __('Item Produk yang Dipesan') }}</h4>
                        
                        {{-- Tabel Dinamis Alpine --}}
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 w-1/2">Produk</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 w-1/4">Stok Saat Ini</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 w-1/4">Jml Order</th>
                                        <th class="px-3 py-2 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="p-2">
                                                <select :name="`products[${index}][product_id]`" 
                                                        x-model="item.product_id" 
                                                        @change="updateProductInfo(index)"
                                                        required 
                                                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                                    <option value="">-- Pilih Produk --</option>
                                                    <template x-for="prod in products" :key="prod.id">
                                                        <option :value="prod.id" x-text="`${prod.name} (SKU: ${prod.sku})`"></option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td class="p-2">
                                                <span x-text="`${item.current_stock} ${item.unit}`" class="text-sm text-gray-600 block bg-gray-50 p-2 rounded"></span>
                                            </td>
                                            <td class="p-2">
                                                <input type="number" :name="`products[${index}][quantity]`" x-model="item.quantity" min="1" required class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                            </td>
                                            <td class="p-2 text-center">
                                                <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-700" :disabled="items.length === 1">Hapus</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-start mb-8">
                             <button type="button" @click="addRow()" class="px-4 py-2 bg-gray-200 border rounded-md text-xs font-bold hover:bg-gray-300">
                                + Tambah Item
                            </button>
                        </div>

                        <div class="flex items-center justify-end border-t pt-4">
                            <a href="{{ route('restock_orders.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md font-bold hover:bg-orange-700 shadow">
                                Kirim Order (Submit)
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
            Alpine.data('restockForm', () => ({
                products: @json($products),
                items: [],

                init() {
                    const oldInput = @json(old('products'));
                    if (oldInput && Object.keys(oldInput).length > 0) {
                        this.items = Object.values(oldInput).map(item => {
                            const product = this.products.find(p => p.id == item.product_id);
                            return { product_id: item.product_id, quantity: item.quantity, current_stock: product ? product.current_stock : 0, unit: product ? product.unit : '' };
                        });
                    } else {
                        this.addRow();
                    }
                },
                addRow() {
                    this.items.push({ product_id: '', quantity: '', current_stock: 0, unit: '' });
                },
                removeRow(index) {
                    if (this.items.length > 1) this.items.splice(index, 1);
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