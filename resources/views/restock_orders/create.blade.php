@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Create New Restock Order (PO)') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="restockForm()">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">{{ __('Purchase Order Details') }}</h3>

                    {{-- Form menuju RestockOrderController@store --}}
                    <form method="POST" action="{{ route('restock_orders.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Kolom Kiri: Info Order --}}
                            <div>
                                {{-- PO Number (Auto) --}}
                                <div class="mb-4">
                                    <label for="po_number" class="block font-medium text-sm text-gray-700">PO Number (Auto)</label>
                                    <input id="po_number" type="text" value="AUTO GENERATED" disabled
                                        class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm">
                                </div>
                                
                                {{-- Supplier ID --}}
                                <div class="mb-4">
                                    <label for="supplier_id" class="block font-medium text-sm text-gray-700">Target Supplier</label>
                                    <select id="supplier_id" name="supplier_id" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('supplier_id') border-red-500 @enderror">
                                        <option value="">-- Select Supplier --</option>
                                        {{-- $suppliers dikirim dari Controller --}}
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Kolom Kanan: Tanggal dan Catatan --}}
                            <div>
                                {{-- Tanggal Order --}}
                                <div class="mb-4">
                                    <label for="order_date" class="block font-medium text-sm text-gray-700">Order Date</label>
                                    <input id="order_date" type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('order_date') border-red-500 @enderror">
                                    @error('order_date')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Expected Delivery Date --}}
                                <div class="mb-4">
                                    <label for="expected_delivery_date" class="block font-medium text-sm text-gray-700">Expected Delivery Date (Optional)</label>
                                    <input id="expected_delivery_date" type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('expected_delivery_date') border-red-500 @enderror">
                                    @error('expected_delivery_date')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Catatan --}}
                                <div class="mb-4">
                                    <label for="notes" class="block font-medium text-sm text-gray-700">Notes</label>
                                    <textarea id="notes" name="notes" rows="1"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="text-lg font-medium text-gray-900 mt-8 mb-4 border-t pt-4">{{ __('Products to Restock') }}</h4>

                        {{-- Daftar Produk (Multi-Entry menggunakan Alpine.js) --}}
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">{{ __('Product Name (SKU)') }}</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">{{ __('Current Stock') }}</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">{{ __('Quantity Needed') }}</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" x-html="renderRows()">
                                    {{-- Rows will be injected by Alpine.js --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Tombol Tambah Produk --}}
                        <div class="flex justify-start mb-8">
                             <button type="button" @click="addRow()" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-400 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                {{ __('Add Product Item') }}
                            </button>
                        </div>


                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end border-t pt-4">
                            <a href="{{ route('restock_orders.index') }}" class="mr-4 text-sm font-semibold text-gray-600 hover:text-gray-900">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Submit Restock Order') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Alpine.js Script untuk Multi-Product Entry --}}
@push('scripts')
<script>
    function restockForm() {
        return {
            // Data produk dari PHP (disimpan sebagai JSON)
            products: @json($products),
            // Array untuk menampung item yang dipilih (product_id, quantity)
            items: [ { product_id: '', quantity: 0, current_stock: 0, unit: '' } ],

            init() {
                const oldInput = {!! json_encode(old('products')) !!};
                if (oldInput && oldInput.length > 0) {
                    this.items = oldInput.map(item => {
                        const product = this.products.find(p => p.id == item.product_id);
                        return { 
                            product_id: item.product_id, 
                            quantity: item.quantity, 
                            current_stock: product ? product.current_stock : 0,
                            unit: product ? product.unit : ''
                        };
                    });
                } else if (this.items.length === 0) {
                     this.addRow(); // Pastikan selalu ada minimal 1 baris kosong
                }
            },
            
            // Tambah baris baru
            addRow() {
                this.items.push({ product_id: '', quantity: 0, current_stock: 0, unit: '' });
                this.$nextTick(() => {
                    const lastRowIndex = this.items.length - 1;
                    this.$refs['product_select_' + lastRowIndex]?.focus();
                });
            },

            // Hapus baris
            removeRow(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            
            // Update stok dan unit saat produk dipilih
            updateProductInfo(index) {
                const selectedProductId = this.items[index].product_id;
                const product = this.products.find(p => p.id == selectedProductId);
                
                this.items[index].current_stock = product ? product.current_stock : 0;
                this.items[index].unit = product ? product.unit : '';

                // Suggest quantity needed (optional based on min_stock)
                // if (product && product.min_stock > product.current_stock) {
                //     this.items[index].quantity = product.min_stock - product.current_stock;
                // }
            },

            // Render baris tabel
            renderRows() {
                return this.items.map((item, index) => {
                    // Label error untuk validasi
                    const quantityError = `<?php echo $errors->has('products.' . ${index} . '.quantity') ? '<p class="text-sm text-red-600 mt-1">' . $errors->first('products.' . ${index} . '.quantity') . '</p>' : ''; ?>`;

                    // Generate Options untuk Select Produk
                    let productOptions = '<option value="">-- Select Product --</option>';
                    this.products.forEach(product => {
                        const selected = (product.id == item.product_id) ? 'selected' : '';
                        productOptions += `<option value="${product.id}" ${selected}>${product.name} (SKU: ${product.sku})</option>`;
                    });

                    return `
                        <tr>
                            <td class="p-2">
                                <select name="products[${index}][product_id]" 
                                        x-ref="product_select_${index}"
                                        x-model="items[${index}].product_id" 
                                        @change="updateProductInfo(${index})"
                                        required 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    ${productOptions}
                                </select>
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-600">
                                <span x-text="items[${index}].current_stock + ' ' + items[${index}].unit"></span>
                            </td>
                            <td class="p-2">
                                <input type="number" 
                                        name="products[${index}][quantity]" 
                                        x-model.number="items[${index}].quantity" 
                                        min="1" 
                                        required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" />
                                ${quantityError}
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" @click="removeRow(${index})" class="text-red-500 hover:text-red-700 disabled:opacity-50" :disabled="items.length === 1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            }
        }
    }
</script>
@endpush