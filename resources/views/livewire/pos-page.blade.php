<div class="flex h-screen bg-gray-100 overflow-hidden" 
     x-data="{ isSuccessModalShow: false, changeAmount: 0, trxId: null }"
     x-on:transaction-completed.window="
        isSuccessModalShow = true;
        changeAmount = $event.detail.change;
        trxId = $event.detail.transactionId;
     "
     x-on:payment-error.window="alert('Error: ' + $event.detail.message);"
>
    
    <div class="w-2/3 flex flex-col border-r border-gray-300">
    
    <div class="p-4 bg-white shadow-sm z-10">

        <div class="flex items-center justify-between mb-4">
            <a href="/admin" class="flex items-center justify-center w-10 h-10 bg-white rounded-full shadow hover:bg-gray-100 transition text-gray-700 border border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-800 ml-4 flex-1">Aplikasi Kasir</h1>
        </div>
        <div class="relative mb-4">
            <input wire:model.live.debounce.300ms="search" type="text" 
            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
            placeholder="Scan barcode atau cari nama produk...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-hide">
                <button wire:click="selectCategory(null)" 
                    class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors
                    {{ is_null($activeCategoryId) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Semua
                </button>
                @foreach($categories as $category)
                    <button wire:click="selectCategory({{ $category->id }})" 
                        class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors
                        {{ $activeCategoryId == $category->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 bg-gray-100">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($products as $product)
                    <div wire:click="addToCart({{ $product->id }})" 
                         class="bg-white rounded-xl shadow hover:shadow-lg cursor-pointer transition-transform transform hover:scale-105 flex flex-col h-full">
                        
                        <div class="h-32 w-full bg-gray-200 rounded-t-xl overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-400">
                                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="p-3 flex flex-col flex-grow justify-between">
                            <div>
                                <h3 class="font-bold text-gray-800 text-sm line-clamp-2">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $product->sku }}</p>
                            </div>
                            <div class="mt-2 flex justify-between items-end">
                                <span class="text-blue-600 font-bold text-sm">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                <span class="text-xs {{ $product->stock > 0 ? 'text-green-500' : 'text-red-500' }}">
                                    Stok: {{ $product->stock }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-10 text-gray-500">
                        <p>Produk tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="w-1/3 bg-white flex flex-col shadow-xl">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                Keranjang
            </h2>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse($cart as $productId => $item)
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $item['name'] }}</h4>
                        <div class="text-xs text-gray-500">
                            Rp {{ number_format($item['price'], 0, ',', '.') }} x {{ $item['qty'] }}
                        </div>
                        <div class="text-blue-600 font-bold text-sm mt-1">
                            Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button wire:click="decreaseQty({{ $productId }})" class="p-1 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700">-</button>
                        <span class="w-6 text-center text-sm font-bold">{{ $item['qty'] }}</span>
                        <button wire:click="increaseQty({{ $productId }})" class="p-1 rounded-full bg-blue-100 hover:bg-blue-200 text-blue-700">+</button>
                        <button wire:click="removeFromCart({{ $productId }})" class="ml-2 text-red-500 hover:text-red-700">x</button>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <p>Keranjang kosong</p>
                </div>
            @endforelse
        </div>

        <div class="p-4 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-600 text-lg">Total</span>
                <span class="text-2xl font-bold text-blue-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            
            <button 
                wire:click="openModal"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-lg flex justify-center items-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                @if(count($cart) == 0) disabled @endif
            >
                Bayar Sekarang
            </button>
        </div>
    </div>

    @if($isShowModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-white text-lg font-bold">Pembayaran</h3>
                <button wire:click="closeModal" class="text-white hover:text-gray-200">X</button>
            </div>

            <div class="p-6 space-y-4">
                <div class="text-center">
                    <p class="text-gray-500 text-sm">Total Tagihan</p>
                    <p class="text-4xl font-bold text-gray-800">Rp {{ number_format($total, 0, ',', '.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uang Diterima</label>
                    <input type="number" wire:model.live="payAmount" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg font-bold"
                        placeholder="0" autofocus>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <button wire:click="$set('payAmount', 10000)" class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200">10.000</button>
                    <button wire:click="$set('payAmount', 20000)" class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200">20.000</button>
                    <button wire:click="$set('payAmount', 50000)" class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200">50.000</button>
                    <button wire:click="$set('payAmount', 100000)" class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200">100.000</button>
                    <button wire:click="$set('payAmount', {{ $total }})" class="px-2 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 col-span-2">Uang Pas</button>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center {{ $change < 0 ? 'text-red-500' : 'text-green-600' }}">
                    <span class="font-semibold">Kembalian</span>
                    <span class="text-xl font-bold">Rp {{ number_format($change, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end">
                <button wire:click="saveTransaction" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow"
                    @if($payAmount < $total) disabled @endif
                >
                    Proses & Cetak Struk
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL NOTIFIKASI PEMBAYARAN BERHASIL (TAMBAHAN) --}}
    <div x-cloak x-show="isSuccessModalShow" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div x-show="isSuccessModalShow" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
            
            <div class="text-green-500 mx-auto mb-4">
                {{-- Penambahan kelas animate-bounce untuk animasi --}}
                <svg class="w-16 h-16 mx-auto animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h3>
            <p class="text-gray-600 mb-4">Transaksi telah selesai.</p>

            <div class="bg-green-50 p-4 rounded-xl mb-6">
                <p class="text-sm text-green-700">Kembalian</p>
                <p class="text-3xl font-extrabold text-green-800" x-text="'Rp ' + (changeAmount).toLocaleString('id-ID')"></p>
            </div>

            <div class="flex flex-col space-y-3">
                <a :href="'{{ route('print.receipt', '') }}/' + trxId" 
                   target="_blank"
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow transition">
                    Cetak Struk
                </a>
                <button @click="isSuccessModalShow = false" 
                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 rounded-lg transition">
                    Kembali ke Kasir
                </button>
            </div>

        </div>
    </div>
</div>