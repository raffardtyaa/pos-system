<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->transaction_code }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; margin: 0; padding: 10px; width: 58mm; }
        .header { text-align: center; margin-bottom: 10px; }
        .store-name { font-size: 16px; font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .item { display: flex; justify-content: space-between; margin-bottom: 2px; }
        .total-section { margin-top: 10px; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; }
        @media print { @page { margin: 0; size: auto; } }
    </style>
</head>
<body onload="window.print(); setTimeout(function(){ window.location.href = '/'; }, 3000);"> 
    <div class="header">
        <div class="store-name">TOKO ICLIK</div>
        <div>Jl. Merdeka No. 1</div>
    </div>
    <div class="divider"></div>
    <div>No: {{ $transaction->transaction_code }}<br>Tgl: {{ $transaction->created_at->format('d-m-Y H:i') }}</div>
    <div class="divider"></div>

    @foreach($transaction->items as $item)
        <div class="item">
            <span>{{ $item->product_name }}</span>
            <span>{{ number_format($item->quantity * $item->price_at_transaction, 0,',','.') }}</span>
        </div>
        <div style="font-size: 10px; color: #555;">{{ $item->quantity }} x {{ number_format($item->price_at_transaction, 0,',','.') }}</div>
    @endforeach

    <div class="divider"></div>
    <div class="item total-section">
        <strong>TOTAL</strong>
        <strong>Rp {{ number_format($transaction->total_amount, 0,',','.') }}</strong>
    </div>
    <div class="item">
        <span>Bayar</span>
        <span>Rp {{ number_format($transaction->cash_amount, 0,',','.') }}</span>
    </div>
    <div class="item">
        <span>Kembali</span>
        <span>Rp {{ number_format($transaction->change_amount, 0,',','.') }}</span>
    </div>
    <div class="divider"></div>
    <div class="footer">Terima Kasih</div>
</body>
</html>