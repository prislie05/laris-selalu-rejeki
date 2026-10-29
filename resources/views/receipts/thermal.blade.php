<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran #{{ $order->order_number }}</title>
    <style>
        @page {
            margin: 4mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .double-divider {
            border-top: 2px dashed #000;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="text-center font-bold" style="font-size: 14px;">TOKO LILY</div>
    <div class="text-center" style="font-size: 10px;">Jl. Sembako Makmur No. 88, Lily City</div>
    <div class="text-center" style="font-size: 10px;">Telp / WA: 0812-3456-7890</div>
    
    <div class="double-divider"></div>

    <table>
        <tr>
            <td>No:</td>
            <td class="text-right">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td>Tgl:</td>
            <td class="text-right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir:</td>
            <td class="text-right">{{ $order->seller?->name ?? 'Online Self-Checkout' }}</td>
        </tr>
        <tr>
            <td>Plg:</td>
            <td class="text-right">{{ $order->customer_name ?: ($order->user?->name ?? 'Walk-In') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table>
        @foreach($order->items as $item)
            <tr>
                <td colspan="2" class="font-bold">{{ $item->product_name }}</td>
            </tr>
            <tr>
                <td>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        <tr class="font-bold">
            <td>TOTAL:</td>
            <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Metode:</td>
            <td class="text-right">{{ strtoupper($order->payment_method) }}</td>
        </tr>
        @if($order->cash_received !== null)
            <tr>
                <td>Tunai:</td>
                <td class="text-right">Rp {{ number_format($order->cash_received, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali:</td>
                <td class="text-right font-bold">Rp {{ number_format($order->cash_change ?? 0, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr>
            <td>Status:</td>
            <td class="text-right font-bold" style="color: #418b24;">LUNAS</td>
        </tr>
    </table>

    <div class="double-divider"></div>

    <div class="text-center" style="margin-top: 8px;">
        *** TERIMA KASIH ***<br>
        Barang yang sudah dibeli<br>
        tidak dapat ditukar/dikembalikan.<br>
        Simpan struk ini sebagai bukti sah.
    </div>
</body>
</html>
