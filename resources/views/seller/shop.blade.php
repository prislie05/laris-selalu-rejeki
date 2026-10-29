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

    <!-- Switcher Bar: Kasir POS vs Etalase Toko -->
    <div class="grid grid-cols-2 gap-1 bg-[#1a4734] p-1 rounded-lg mb-3">
        <button 
            type="button" 
            @click="activeTab = 'pos'"
            :class="activeTab === 'pos' ? 'bg-[#f9dd9c] text-[#1a4734] font-black' : 'text-white font-bold'"
            class="py-1.5 text-xs rounded transition-colors flex items-center justify-center space-x-1"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span>Kasir POS</span>
        </button>
        <button 
            type="button" 
            @click="activeTab = 'catalog'"
            :class="activeTab === 'catalog' ? 'bg-[#f9dd9c] text-[#1a4734] font-black' : 'text-white font-bold'"
            class="py-1.5 text-xs rounded transition-colors flex items-center justify-center space-x-1"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <span>Etalase Toko</span>
        </button>
    </div>

    <!-- Panel Stok Tertipis (Banner Peringatan Seller) -->
    <div class="bg-white rounded-lg border-2 border-[#870903] p-3 mb-3">
        <div class="flex items-center justify-between pb-2 border-b border-gray-200">
            <h3 class="font-black text-xs text-[#870903] flex items-center space-x-1 uppercase">
                <svg class="w-4 h-4 text-[#870903]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Daftar Stok Tertipis (Perlu Restock)</span>
            </h3>
        </div>
        <div class="divide-y divide-gray-100 pt-1">
            @foreach($thinnestProducts as $tp)
                <div class="py-1.5 flex items-center justify-between text-xs">
                    <span class="font-semibold text-gray-800 truncate max-w-[210px]">{{ $tp->name }}</span>
                    <div class="flex items-center space-x-1.5">
                        <span class="font-black px-1.5 py-0.5 rounded text-[10px] {{ $tp->stock <= 0 ? 'bg-[#870903] text-white' : ($tp->stock <= 5 ? 'bg-[#e90c00] text-white' : 'bg-yellow-400 text-black') }}">
                            {{ $tp->stock <= 0 ? 'HABIS' : 'Sisa ' . $tp->stock }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 1: KASIR / POS WORKSPACE -->
    <div x-show="activeTab === 'pos'">
        
        <!-- Quick Add Product to POS -->
        <div class="bg-white rounded-lg border-2 border-[#1a4734] p-3 mb-3">
            <h3 class="font-extrabold text-xs text-[#1a4734] mb-2 uppercase">1. Pilih Barang Kasir</h3>
            <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1 no-scrollbar">
                @foreach($products as $p)
                    <button 
                        type="button" 
                        @click="addToPos({{ json_encode($p) }})"
                        {{ $p->stock <= 0 ? 'disabled' : '' }}
                        class="text-left bg-[#f9dd9c]/30 hover:bg-[#f9dd9c] p-2 rounded border border-[#1a4734] disabled:opacity-40 disabled:cursor-not-allowed flex flex-col justify-between"
                    >
                        <p class="font-bold text-[11px] text-[#1a4734] line-clamp-1">{{ $p->name }}</p>
                        <div class="flex justify-between items-center mt-1">
                            <span class="font-black text-xs text-[#870903]">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                            <span class="text-[9px] font-bold {{ $p->stock <= 0 ? 'text-[#870903]' : 'text-gray-500' }}">
                                Stok: {{ $p->stock }}
                            </span>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- POS Checkout Form -->
        <div class="bg-white rounded-lg border-2 border-[#1a4734] p-3">
            <h3 class="font-extrabold text-xs text-[#1a4734] mb-2 uppercase flex items-center justify-between pb-1 border-b border-gray-200">
                <span>2. Transaksi Kasir POS</span>
                <span class="text-[10px] font-bold text-gray-500" x-text="posItems.length + ' item dipilih'"></span>
            </h3>

            <!-- POS Items List -->
            <div class="space-y-2 mb-3 max-h-40 overflow-y-auto pr-1">
                <template x-if="posItems.length === 0">
                    <p class="text-xs text-gray-500 italic text-center py-4 bg-gray-50 rounded">
                        Belum ada barang dipilih. Klik barang di atas untuk menambahkan.
                    </p>
                </template>

                <template x-for="(item, index) in posItems" :key="index">
                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded border border-gray-200 text-xs">
                        <div class="flex-1 min-w-0 pr-2">
                            <p class="font-bold text-[#1a4734] truncate" x-text="item.name"></p>
                            <p class="text-[10px] text-[#870903] font-bold" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.price)"></p>
                        </div>

                        <!-- Quantity control -->
                        <div class="flex items-center space-x-1.5">
                            <button 
                                type="button" 
                                @click="if(item.quantity > 1) { item.quantity--; } else { removeFromPos(index); }"
                                class="w-5 h-5 bg-[#1a4734] text-white rounded font-black text-xs flex items-center justify-center"
                            >-</button>
                            <span class="w-6 text-center font-black" x-text="item.quantity"></span>
                            <button 
                                type="button" 
                                @click="if(item.quantity < item.stock) { item.quantity++; } else { alert('Stok maksimal!'); }"
                                class="w-5 h-5 bg-[#1a4734] text-white rounded font-black text-xs flex items-center justify-center"
                            >+</button>
                            <button 
                                type="button" 
                                @click="removeFromPos(index)" 
                                class="text-[#870903] font-black pl-1"
                            >&times;</button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Form submission to pos.checkout -->
            <form action="{{ route('seller.pos.checkout') }}" method="POST" class="space-y-3">
                @csrf

                <!-- Hidden inputs for items -->
                <template x-for="(item, idx) in posItems" :key="idx">
                    <div>
                        <input type="hidden" :name="'items[' + idx + '][product_id]'" :value="item.product_id">
                        <input type="hidden" :name="'items[' + idx + '][quantity]'" :value="item.quantity">
                    </div>
                </template>

                <div>
                    <label class="block text-[11px] font-bold text-[#1a4734] mb-0.5">Nama Pelanggan Walk-In</label>
                    <input 
                        type="text" 
                        name="customer_name" 
                        x-model="customerName" 
                        class="w-full bg-[#f9dd9c]/30 border border-[#1a4734] px-2.5 py-1.5 rounded text-xs font-semibold text-[#1a4734] focus:outline-none"
                    >
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#1a4734] mb-0.5">Metode Pembayaran Kasir</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="border-2 rounded p-2 flex items-center space-x-1.5 cursor-pointer" :class="paymentMethod === 'cash' ? 'border-[#418b24] bg-green-50' : 'border-gray-300'">
                            <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="text-[#418b24]">
                            <span class="text-xs font-bold text-[#1a4734]">CASH (Tunai)</span>
                        </label>
                        <label class="border-2 rounded p-2 flex items-center space-x-1.5 cursor-pointer" :class="paymentMethod === 'qris' ? 'border-[#418b24] bg-green-50' : 'border-gray-300'">
                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="text-[#418b24]">
                            <span class="text-xs font-bold text-[#1a4734]">QRIS (Non-Tunai)</span>
                        </label>
                    </div>
                </div>

                <!-- Total Belanja Calculation -->
                <div class="bg-gray-100 p-2.5 rounded border border-gray-300">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-700">Total Belanja:</span>
                        <span class="font-black text-base text-[#870903]">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalPos)"></span>
                        </span>
                    </div>

                    <!-- Cash Input & Instant Change Calculation -->
                    <template x-if="paymentMethod === 'cash'">
                        <div class="mt-2.5 pt-2 border-t border-gray-300 space-y-2">
                            <div>
                                <label class="block text-[11px] font-extrabold text-[#1a4734] mb-1">
                                    Jumlah Uang Diterima (Rp)
                                </label>
                                <input 
                                    type="number" 
                                    name="cash_received" 
                                    x-model="cashReceived" 
                                    min="0"
                                    step="500"
                                    placeholder="Contoh: 50000" 
                                    class="w-full bg-white border-2 border-[#1a4734] px-3 py-1.5 rounded text-sm font-black text-[#1a4734] focus:outline-none"
                                >
                            </div>

                            <div class="flex justify-between items-center pt-1 text-xs">
                                <span class="font-extrabold text-[#1a4734]">Nominal Kembalian:</span>
                                <span 
                                    class="font-black text-base"
                                    :class="isCashSufficient ? 'text-[#418b24]' : 'text-[#870903]'"
                                >
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(cashChange)"></span>
                                </span>
                            </div>

                            <template x-if="!isCashSufficient && totalPos > 0">
                                <p class="text-[10px] font-black text-[#870903]">
                                    * Peringatan: Uang diterima kurang dari total belanja!
                                </p>
                            </template>
                        </div>
                    </template>
                </div>

                <button 
                    type="submit" 
                    :disabled="posItems.length === 0 || !isCashSufficient || totalPos <= 0"
                    class="w-full bg-[#418b24] text-white py-2.5 rounded font-black text-xs uppercase tracking-wider hover:opacity-90 active:scale-[0.99] transition-transform flex items-center justify-center space-x-1.5 disabled:opacity-40 disabled:cursor-not-allowed"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Proses Kasir & Terbitkan Struk</span>
                </button>
            </form>
        </div>

    </div>

    <!-- TAB 2: ETALASE TOKO VIEW -->
    <div x-show="activeTab === 'catalog'" class="space-y-2.5">
        @foreach($products as $prod)
            <div class="bg-white p-3 rounded-lg border-2 border-[#1a4734] flex items-center justify-between">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <div class="w-12 h-12 bg-[#f9dd9c]/40 rounded border border-[#1a4734] overflow-hidden flex-shrink-0 flex items-center justify-center">
                        @if($prod->image_url)
                            <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="font-black text-[#1a4734] text-xs">TL</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-extrabold text-xs text-[#1a4734] truncate">{{ $prod->name }}</h4>
                        <span class="text-[10px] text-gray-500">{{ $prod->category }}</span>
                        <p class="font-black text-xs text-[#870903]">Rp {{ number_format($prod->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="font-black text-xs px-2 py-0.5 rounded {{ $prod->stock <= 0 ? 'bg-[#870903] text-white' : ($prod->stock <= 5 ? 'bg-[#e90c00] text-white' : 'bg-green-100 text-[#418b24]') }}">
                        Stok: {{ $prod->stock }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
