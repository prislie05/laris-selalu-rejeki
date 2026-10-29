@extends('layouts.mobile', ['title' => 'Toko & Kasir POS - Toko Lily'])

@section('content')
<div class="py-2" x-data="{
    activeTab: 'pos', // 'pos' or 'catalog'
    products: {{ json_encode($products) }},
    posItems: [],
    paymentMethod: 'cash',
    cashReceived: 0,
    customerName: 'Pelanggan Walk-In',
    notes: '',

    addToPos(product) {
        if (product.stock <= 0) {
            alert('Stok barang habis!');
            return;
        }
        let existing = this.posItems.find(i => i.product_id === product.id);
        if (existing) {
            if (existing.quantity < product.stock) {
                existing.quantity++;
            } else {
                alert('Maksimal stok tersedia adalah ' + product.stock);
            }
        } else {
            this.posItems.push({
                product_id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                stock: parseInt(product.stock),
                quantity: 1
            });
        }
    },
    removeFromPos(index) {
        this.posItems.splice(index, 1);
    },
    get totalPos() {
        return this.posItems.reduce((sum, i) => sum + (i.price * i.quantity), 0);
    },
    get cashChange() {
        let diff = (parseFloat(this.cashReceived) || 0) - this.totalPos;
        return diff >= 0 ? diff : 0;
    },
    get isCashSufficient() {
        if (this.paymentMethod !== 'cash') return true;
        return (parseFloat(this.cashReceived) || 0) >= this.totalPos;
    }
}">

    <!-- Switcher Bar: Kasir POS vs Etalase Toko (Tombol Besar & Nyaman) -->
    <div class="grid grid-cols-2 gap-2 bg-[#1a4734] p-1.5 rounded-xl mb-4">
        <button 
            type="button" 
            @click="activeTab = 'pos'"
            :class="activeTab === 'pos' ? 'bg-[#f9dd9c] text-[#1a4734] font-black' : 'text-white font-bold'"
            class="min-h-[50px] py-3 text-lg rounded-lg transition-all flex items-center justify-center space-x-2 active:scale-95"
        >
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span>Kasir POS</span>
        </button>
        <button 
            type="button" 
            @click="activeTab = 'catalog'"
            :class="activeTab === 'catalog' ? 'bg-[#f9dd9c] text-[#1a4734] font-black' : 'text-white font-bold'"
            class="min-h-[50px] py-3 text-lg rounded-lg transition-all flex items-center justify-center space-x-2 active:scale-95"
        >
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <span>Etalase Toko</span>
        </button>
    </div>

    <!-- Panel Stok Tertipis (Banner Peringatan Seller dengan Teks Jelas) -->
    <div class="bg-white rounded-xl border-2 border-[#870903] p-4 mb-4 shadow-sm">
        <div class="flex items-center justify-between pb-2.5 border-b-2 border-gray-200">
            <h3 class="font-black text-lg text-[#870903] flex items-center space-x-2 uppercase">
                <svg class="w-6 h-6 text-[#870903] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Daftar Stok Tertipis</span>
            </h3>
        </div>
        <div class="divide-y-2 divide-gray-100 pt-1">
            @foreach($thinnestProducts as $tp)
                <div class="py-2.5 flex flex-wrap items-center justify-between gap-1 text-base">
                    <span class="font-bold text-gray-800 break-words max-w-[260px]">{{ $tp->name }}</span>
                    <div class="flex items-center space-x-2 flex-shrink-0">
                        <span class="font-black px-3 py-1 rounded-md text-sm {{ $tp->stock <= 0 ? 'bg-[#870903] text-white' : ($tp->stock <= 5 ? 'bg-[#e90c00] text-white' : 'bg-yellow-400 text-black') }}">
                            {{ $tp->stock <= 0 ? 'HABIS' : 'Sisa ' . $tp->stock }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 1: KASIR / POS WORKSPACE -->
    <div x-show="activeTab === 'pos'">
        
        <!-- 1. Quick Add Product to POS -->
        <div class="bg-white rounded-xl border-2 border-[#1a4734] p-4 mb-4 shadow-sm">
            <h3 class="font-black text-lg text-[#1a4734] mb-3 uppercase flex items-center space-x-2">
                <span>1. Pilih Barang Kasir</span>
            </h3>
            <div class="grid grid-cols-2 gap-2.5 max-h-56 overflow-y-auto pr-1 no-scrollbar">
                @foreach($products as $p)
                    <button 
                        type="button" 
                        @click="addToPos({{ json_encode($p) }})"
                        {{ $p->stock <= 0 ? 'disabled' : '' }}
                        class="text-left bg-[#f9dd9c]/30 hover:bg-[#f9dd9c] p-3 rounded-xl border-2 border-[#1a4734] disabled:opacity-40 disabled:cursor-not-allowed flex flex-col justify-between active:scale-95 transition-all"
                    >
                        <p class="font-bold text-base text-[#1a4734] line-clamp-2 leading-tight">{{ $p->name }}</p>
                        <div class="flex flex-wrap justify-between items-center gap-1 mt-2">
                            <span class="font-black text-lg text-[#870903]">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                            <span class="text-xs font-bold {{ $p->stock <= 0 ? 'text-[#870903]' : 'text-gray-600' }}">
                                Stok: {{ $p->stock }}
                            </span>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- 2. POS Checkout Form -->
        <div class="bg-white rounded-xl border-2 border-[#1a4734] p-4 shadow-sm">
            <h3 class="font-black text-lg text-[#1a4734] mb-3 uppercase flex items-center justify-between pb-2 border-b-2 border-gray-200">
                <span>2. Transaksi Kasir POS</span>
                <span class="text-sm font-bold text-gray-600" x-text="posItems.length + ' item dipilih'"></span>
            </h3>

            <!-- POS Items List -->
            <div class="space-y-3 mb-4 max-h-48 overflow-y-auto pr-1">
                <template x-if="posItems.length === 0">
                    <p class="text-base text-gray-500 italic text-center py-5 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        Belum ada barang dipilih. Sentuh barang di atas untuk menambahkan.
                    </p>
                </template>

                <template x-for="(item, index) in posItems" :key="index">
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200 gap-2">
                        <div class="flex-1 min-w-0 pr-2">
                            <p class="font-bold text-base text-[#1a4734] truncate" x-text="item.name"></p>
                            <p class="text-sm text-[#870903] font-black" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.price)"></p>
                        </div>

                        <!-- Quantity Control (Ukuran Minimal 44x44px w-11 h-11) -->
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <button 
                                type="button" 
                                @click="if(item.quantity > 1) { item.quantity--; } else { removeFromPos(index); }"
                                class="w-10 h-10 bg-[#1a4734] text-white rounded-lg font-black text-xl flex items-center justify-center active:scale-95"
                            >-</button>
                            <span class="w-8 text-center font-black text-xl" x-text="item.quantity"></span>
                            <button 
                                type="button" 
                                @click="if(item.quantity < item.stock) { item.quantity++; } else { alert('Stok maksimal!'); }"
                                class="w-10 h-10 bg-[#1a4734] text-white rounded-lg font-black text-xl flex items-center justify-center active:scale-95"
                            >+</button>
                            <button 
                                type="button" 
                                @click="removeFromPos(index)" 
                                class="text-[#870903] font-black text-2xl pl-1 hover:opacity-80"
                            >&times;</button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Form submission to pos.checkout -->
            <form action="{{ route('seller.pos.checkout') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Hidden inputs for items -->
                <template x-for="(item, idx) in posItems" :key="idx">
                    <div>
                        <input type="hidden" :name="'items[' + idx + '][product_id]'" :value="item.product_id">
                        <input type="hidden" :name="'items[' + idx + '][quantity]'" :value="item.quantity">
                    </div>
                </template>

                <div>
                    <label class="block text-base font-bold text-[#1a4734] mb-1">Nama Pembeli Walk-In</label>
                    <input 
                        type="text" 
                        name="customer_name" 
                        x-model="customerName" 
                        class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-xl text-lg font-bold text-[#1a4734] focus:outline-none"
                    >
                </div>

                <div>
                    <label class="block text-base font-bold text-[#1a4734] mb-1.5">Metode Pembayaran Kasir</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="border-2 rounded-xl p-3 flex items-center space-x-2.5 cursor-pointer" :class="paymentMethod === 'cash' ? 'border-[#418b24] bg-green-50' : 'border-gray-300'">
                            <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="w-5 h-5 text-[#418b24]">
                            <span class="text-base font-black text-[#1a4734]">CASH (Tunai)</span>
                        </label>
                        <label class="border-2 rounded-xl p-3 flex items-center space-x-2.5 cursor-pointer" :class="paymentMethod === 'qris' ? 'border-[#418b24] bg-green-50' : 'border-gray-300'">
                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="w-5 h-5 text-[#418b24]">
                            <span class="text-base font-black text-[#1a4734]">QRIS</span>
                        </label>
                    </div>
                </div>

                <!-- Total Belanja & Hitung Kembalian Kasir -->
                <div class="bg-gray-100 p-4 rounded-xl border-2 border-gray-300">
                    <div class="flex flex-wrap justify-between items-center gap-1 text-base">
                        <span class="font-extrabold text-gray-700">Total Belanja:</span>
                        <span class="font-black text-3xl text-[#870903]">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalPos)"></span>
                        </span>
                    </div>

                    <!-- Cash Input & Instant Change Calculation -->
                    <template x-if="paymentMethod === 'cash'">
                        <div class="mt-3.5 pt-3 border-t-2 border-gray-300 space-y-3">
                            <div>
                                <label class="block text-base font-black text-[#1a4734] mb-1.5">
                                    Jumlah Uang Diterima (Rp)
                                </label>
                                <input 
                                    type="number" 
                                    name="cash_received" 
                                    x-model="cashReceived" 
                                    min="0"
                                    step="500"
                                    placeholder="Contoh: 50000" 
                                    class="w-full bg-white border-2 border-[#1a4734] px-4 py-3 rounded-xl text-2xl font-black text-[#1a4734] focus:outline-none"
                                >
                            </div>

                            <div class="flex flex-wrap justify-between items-center pt-2 gap-1">
                                <span class="font-extrabold text-lg text-[#1a4734]">Nominal Kembalian:</span>
                                <span 
                                    class="font-black text-3xl"
                                    :class="isCashSufficient ? 'text-[#418b24]' : 'text-[#870903]'"
                                >
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(cashChange)"></span>
                                </span>
                            </div>

                            <template x-if="!isCashSufficient && totalPos > 0">
                                <p class="text-sm font-black text-[#870903] bg-red-100 p-2 rounded-lg border border-[#870903]">
                                    * Peringatan: Uang yang diterima kurang dari total belanja!
                                </p>
                            </template>
                        </div>
                    </template>
                </div>

                <button 
                    type="submit" 
                    :disabled="posItems.length === 0 || !isCashSufficient || totalPos <= 0"
                    class="w-full bg-[#418b24] text-white min-h-[52px] py-3.5 rounded-xl font-black text-xl uppercase tracking-wider hover:opacity-90 active:scale-95 transition-all flex items-center justify-center space-x-2 disabled:opacity-40 disabled:cursor-not-allowed shadow-md"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Proses Kasir & Terbitkan Struk</span>
                </button>
            </form>
        </div>

    </div>

    <!-- TAB 2: ETALASE TOKO VIEW -->
    <div x-show="activeTab === 'catalog'" class="space-y-3">
        @foreach($products as $prod)
            <div class="bg-white p-4 rounded-xl border-2 border-[#1a4734] flex flex-wrap items-center justify-between gap-2 shadow-sm">
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-black px-2.5 py-0.5 rounded bg-gray-100 text-gray-700 uppercase border border-gray-300">
                        {{ $prod->category }}
                    </span>
                    <h4 class="font-bold text-xl text-[#1a4734] break-words mt-1">{{ $prod->name }}</h4>
                    <p class="font-black text-2xl text-[#870903] mt-1">Rp {{ number_format($prod->price, 0, ',', '.') }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="font-black text-base px-3 py-1.5 rounded-lg {{ $prod->stock <= 0 ? 'bg-[#870903] text-white' : ($prod->stock <= 5 ? 'bg-[#e90c00] text-white' : 'bg-green-100 text-[#418b24]') }}">
                        Stok: {{ $prod->stock }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
