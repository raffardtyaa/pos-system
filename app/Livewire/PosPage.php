<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
#[Title('Kasir - Point of Sales')]
class PosPage extends Component
{
    public $search = '';
    public $activeCategoryId = null;
    public $cart = []; 
    public $total = 0;

    // Variabel untuk Modal Pembayaran
    public $isShowModal = false;
    public $payAmount = 0; // Uang yang dibayarkan
    public $change = 0;    // Kembalian
    public $transactionId = null; // TAMBAHAN: Menyimpan ID Transaksi untuk pop-up notifikasi

    public function updatedSearch() { $this->resetPage(); }

    public function selectCategory($categoryId)
    {
        $this->activeCategoryId = $this->activeCategoryId == $categoryId ? null : $categoryId;
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->selling_price,
                'image' => $product->image,
                'qty' => 1
            ];
        }
        $this->calculateTotal();
    }

    public function increaseQty($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
            $this->calculateTotal();
        }
    }

    public function decreaseQty($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] > 1) {
                $this->cart[$productId]['qty']--;
            } else {
                unset($this->cart[$productId]);
            }
            $this->calculateTotal();
        }
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['qty'];
        }
        $this->calculateChange(); // Hitung ulang kembalian saat total berubah
    }

    // --- LOGIKA PEMBAYARAN ---

    public function openModal()
    {
        $this->payAmount = 0;
        $this->change = -$this->total;
        $this->isShowModal = true;
    }

    public function closeModal()
    {
        $this->isShowModal = false;
    }

    // Update real-time saat ketik nominal uang
    public function updatedPayAmount()
    {
        $this->calculateChange();
    }

    public function calculateChange()
    {
        // Pastikan payAmount dianggap angka
        $pay = (int)$this->payAmount;
        $this->change = $pay - $this->total;
    }

    public function saveTransaction()
    {
        if ($this->payAmount < $this->total) {
            // Tambahkan event error jika uang kurang
            $this->dispatch('payment-error', message: 'Uang yang dibayarkan kurang dari total tagihan.');
            return;
        }

        DB::transaction(function () {
            // 1. Buat Transaksi Utama
            $trx = Transaction::create([
                'transaction_code' => 'TRX-' . time(),
                'total_amount' => $this->total,
                'payment_method' => 'cash',
                'cash_amount' => $this->payAmount,
                'change_amount' => $this->change,
                'status' => 'completed'
            ]);

            // 2. Simpan Detail Item & Kurangi Stok
            foreach ($this->cart as $item) {
                TransactionItem::create([
                    'transaction_id' => $trx->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['qty'],
                    'price_at_transaction' => $item['price'],
                ]);

                // Kurangi stok produk
                $product = Product::find($item['id']);
                if($product) {
                    $product->decrement('stock', $item['qty']);
                }
            }

            // 3. Simpan ID Transaksi untuk pop-up, Reset Cart & Tutup Modal Pembayaran
            $this->transactionId = $trx->id;
            $this->cart = [];
            $this->total = 0;
            $this->isShowModal = false;

            // 4. Mengganti redirect dengan dispatch event untuk menampilkan pop-up di client
            $this->dispatch('transaction-completed', change: $this->change, transactionId: $this->transactionId);
        });
    }

    public function render()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($this->activeCategoryId, function ($query) {
                $query->where('category_id', $this->activeCategoryId);
            })
            ->get();

        return view('livewire.pos-page', [
            'products' => $products,
            'categories' => Category::all(),
        ]);
    }
}