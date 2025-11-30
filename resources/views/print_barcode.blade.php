<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode - {{ $product->name }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .label-container { 
            border: 2px dashed #333; 
            padding: 20px; 
            text-align: center; 
            width: 300px;
        }
        .product-name { 
            font-size: 16px; 
            font-weight: bold; 
            margin-bottom: 10px; 
            text-transform: uppercase;
        }
        img { 
            width: 80%; 
            height: auto; 
        }
        .price { 
            font-size: 18px; 
            font-weight: bold; 
            margin-top: 10px; 
        }
        .sku {
            font-size: 12px;
            color: #555;
            margin-top: 5px;
        }
        /* Hilangkan elemen lain saat diprint */
        @media print {
            body { align-items: flex-start; margin: 0; }
            .label-container { border: none; }
        }
    </style>
</head>
<body onload="window.print()"> 
    <div class="label-container">
        <div class="product-name">{{ $product->name }}</div>
        
        <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Barcode">
        
        <div class="sku">{{ $product->barcode }}</div>
        <div class="price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
    </div>

</body>
</html>