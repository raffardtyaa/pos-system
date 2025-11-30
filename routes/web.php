<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintBarcodeController;
use App\Livewire\PosPage;

// Halaman Utama (Kasir)
Route::get('/', PosPage::class)->name('pos');

// Cetak Barcode Produk
Route::get('/print-barcode/{product}', PrintBarcodeController::class)->name('print.barcode');

// Cetak Struk Belanja (INI YANG TADI KURANG/ERROR)
Route::get('/print-receipt/{transaction}', function (\App\Models\Transaction $transaction) {
    return view('print_receipt', compact('transaction'));
})->name('print.receipt');