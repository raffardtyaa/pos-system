<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PrintBarcodeController extends Controller
{
    public function __invoke(Product $product)
    {
        $generator = new BarcodeGeneratorPNG();
        
        // Membuat gambar barcode dalam format Base64 agar bisa langsung tampil di HTML
        $barcodeData = $generator->getBarcode($product->barcode, $generator::TYPE_CODE_128);
        $barcodeBase64 = base64_encode($barcodeData);

        return view('print_barcode', compact('product', 'barcodeBase64'));
    }
}