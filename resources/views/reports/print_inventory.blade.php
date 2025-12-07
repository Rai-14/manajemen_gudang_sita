<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventori - WMS Pro</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #555; }
        
        .summary { margin-bottom: 20px; display: flex; justify-content: space-between; }
        .summary-box { border: 1px solid #ddd; padding: 10px; width: 48%; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #888; }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>LAPORAN INVENTORI GUDANG</h1>
        <p>WMS PRO SYSTEM | Dicetak pada: {{ date('d F Y, H:i') }} | Oleh: {{ Auth::user()->name }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <strong>Total Item Fisik:</strong> {{ number_format($totalItems) }} Unit
        </div>
        <div class="summary-box">
            <strong>Total Nilai Aset:</strong> Rp {{ number_format($totalAssetValue, 0, ',', '.') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">SKU</th>
                <th style="width: 30%">Nama Produk</th>
                <th style="width: 15%">Kategori</th>
                <th style="width: 10%" class="text-center">Stok</th>
                <th style="width: 15%" class="text-right">Harga Beli</th>
                <th style="width: 15%" class="text-right">Total Aset</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td class="text-center">{{ $product->current_stock }} {{ $product->unit }}</td>
                <td class="text-right">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($product->current_stock * $product->purchase_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>TOTAL NILAI ASET</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem komputer.</p>
    </div>

</body>
</html>