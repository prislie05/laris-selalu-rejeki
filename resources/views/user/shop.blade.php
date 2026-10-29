@extends('layouts.mobile', ['title' => 'Belanja - Toko Lily'])

@section('content')
<div class="py-2" x-data="{ buyNowModal: false, buyProduct: null, buyQty: 1 }">

    <!-- Search Bar (Ramah Lansia: Input & Tombol Besar) -->
    <div class="mb-4">
        <form action="{{ route('user.shop') }}" method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari barang belanjaan..." 
                    class="w-full bg-white border-2 border-[#1a4734] pl-11 pr-4 py-3 rounded-xl text-lg font-bold text-[#1a4734] placeholder-gray-500 focus:outline-none"
                >
                <svg class="w-6 h-6 text-[#1a4734] absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button type="submit" class="bg-[#1a4734] text-[#f9dd9c] px-5 py-3 rounded-xl font-black text-lg min-h-[50px] flex items-center justify-center hover:bg-[#143728] active:scale-95 transition-all">
                Cari
            </button>
            @if($search)
                <a href="{{ route('user.shop') }}" class="bg-[#870903] text-white px-3.5 py-3 rounded-xl font-black text-xl min-h-[50px] flex items-center justify-center" title="Reset Pencarian">
                    &times;
                </a>
            @endif
        </form>
    </div>

    <!-- Category Filter Pills (Tombol Kategori Lebar & Nyaman Disentuh) -->
    <div class="flex gap-2 overflow-x-auto pb-2.5 mb-4 no-scrollbar">
        <a 
            href="{{ route('user.shop', array_merge(request()->except('category'), ['category' => 'Semua'])) }}" 
            class="px-4 py-2 rounded-full text-base font-bold whitespace-nowrap border-2 border-[#1a4734] {{ $selectedCategory === 'Semua' ? 'bg-[#1a4734] text-[#f9dd9c]' : 'bg-white text-[#1a4734]' }}"
        >
            Semua
        </a>
        @foreach($categories as $cat)
            <a 
                href="{{ route('user.shop', array_merge(request()->except('category'), ['category' => $cat])) }}" 
                class="px-4 py-2 rounded-full text-base font-bold whitespace-nowrap border-2 border-[#1a4734] {{ $selectedCategory === $cat ? 'bg-[#1a4734] text-[#f9dd9c]' : 'bg-white text-[#1a4734]' }}"
            >
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Product Grid Catalog (Kartu Produk Jelas & Besar) -->
    <div class="space-y-4">
        @forelse($products as $prod)
            <div class="bg-white rounded-xl border-2 border-[#1a4734] p-4 flex flex-col justify-between shadow-sm">
                <!-- Product Information -->
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center justify-between gap-1 mb-2">
                        <span class="text-xs font-black px-2.5 py-0.5 rounded bg-[#f9dd9c] text-[#1a4734] uppercase border border-[#1a4734]/30">
                            {{ $prod->category }}
                        </span>
                        @if($prod->stock <= 0)
                            <span class="text-sm font-black px-2.5 py-1 rounded bg-[#870903] text-white">
                                STOK HABIS
                            </span>
                        @elseif($prod->stock <= 5)
                            <span class="text-sm font-black px-2.5 py-1 rounded bg-[#e90c00] text-white">
                                Sisa {{ $prod->stock }}
                            </span>
                        @else
                            <span class="text-sm font-bold text-gray-600">
                                Stok: {{ $prod->stock }}
                            </span>
                        @endif
                    </div>

                    <!-- Nama Barang: 20px - 22px font-bold -->
                    <h3 class="font-bold text-xl text-[#1a4734] leading-tight break-words">
                        {{ $prod->name }}
                    </h3>

                    <!-- Deskripsi: 18px leading-relaxed -->
                    <p class="text-base text-gray-700 leading-relaxed mt-1 line-clamp-2">
                        {{ $prod->description ?? 'Barang kebutuhan harian pilihan Toko Lily' }}
                    </p>

                    <!-- Harga: 24px - 28px font-extrabold -->
                    <div class="mt-2 font-black text-2xl text-[#870903]">
                        Rp {{ number_format($prod->price, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Action Buttons: Minimal 18px font-bold & Touch Area Lega -->
                <div class="grid grid-cols-2 gap-2.5 mt-4 pt-3 border-t-2 border-gray-200">
                    <!-- Tambah ke Keranjang -->
                    <form action="{{ route('user.cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $prod->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button 
                            type="submit" 
                            {{ $prod->stock <= 0 ? 'disabled' : '' }}
                            class="w-full bg-[#f9dd9c] text-[#1a4734] border-2 border-[#1a4734] min-h-[50px] py-3 rounded-lg font-black text-lg flex items-center justify-center space-x-1.5 hover:bg-[#ebd090] active:scale-95 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>+ Keranjang</span>
                        </button>
                    </form>

                    <!-- Beli Sekarang -->
                    <button 
                        type="button"
                        {{ $prod->stock <= 0 ? 'disabled' : '' }}
                        @click="buyProduct = {{ json_encode($prod) }}; buyQty = 1; buyNowModal = true"
                        class="w-full bg-[#418b24] text-white border-2 border-[#418b24] min-h-[50px] py-3 rounded-lg font-black text-lg flex items-center justify-center space-x-1.5 hover:opacity-90 active:scale-95 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <span>Beli Sekarang</span>
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-xl border-2 border-[#1a4734] text-center">
                <p class="font-extrabold text-xl text-[#870903]">Barang Tidak Ditemukan</p>
                <p class="text-base text-gray-700 mt-2">Coba gunakan kata kunci pencarian yang lain atau pilih kategori Semua.</p>
            </div>
        @endforelse
    </div>

    <!-- Buy Now Modal (Formulir Beli Langsung) -->
    <div 
        x-cloak 
        x-show="buyNowModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4 py-6"
        @click.self="buyNowModal = false"
    >
        <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[420px] rounded-xl p-5 text-[#1a4734] shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b-2 border-[#1a4734] mb-4">
                <h3 class="font-black text-xl text-[#1a4734] uppercase tracking-wide">Beli Langsung</h3>
                <button type="button" @click="buyNowModal = false" class="text-3xl font-black text-[#870903] leading-none p-1">&times;</button>
            </div>

            <template x-if="buyProduct">
                <form action="{{ route('user.checkout') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="product_id" :value="buyProduct.id">

                    <div class="bg-white p-3.5 rounded-lg border-2 border-[#1a4734]">
                        <p class="font-bold text-xl leading-tight" x-text="buyProduct.name"></p>
                        <p class="text-2xl font-black text-[#870903] mt-1.5">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(buyProduct.price)"></span>
                        </p>
                        <p class="text-sm font-semibold text-gray-600 mt-1">Stok tersedia: <span class="font-bold" x-text="buyProduct.stock"></span></p>
                    </div>

                    <div>
                        <label class="block text-base font-bold text-[#1a4734] mb-2">Jumlah Beli</label>
                        <div class="flex items-center space-x-3">
                            <button 
                                type="button" 
                                @click="if(buyQty > 1) buyQty--" 
                                class="w-12 h-12 bg-[#1a4734] text-white rounded-lg font-black text-2xl flex items-center justify-center active:scale-95"
                            >-</button>
                            <input 
                                type="number" 
                                name="quantity" 
                                x-model="buyQty" 
                                min="1" 
                                :max="buyProduct.stock" 
                                class="w-24 text-center bg-white border-2 border-[#1a4734] py-2 font-black text-2xl rounded-lg focus:outline-none"
                            >
                            <button 
                                type="button" 
                                @click="if(buyQty < buyProduct.stock) buyQty++" 
                                class="w-12 h-12 bg-[#1a4734] text-white rounded-lg font-black text-2xl flex items-center justify-center active:scale-95"
                            >+</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-base font-bold text-[#1a4734] mb-1.5">Metode Pembayaran</label>
                        <select name="payment_method" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-lg text-lg font-bold text-[#1a4734] focus:outline-none">
                            <option value="qris">QRIS (Otomatis / Instan)</option>
                            <option value="cash">Tunai (Bayar di Tempat)</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>

                    <div class="bg-white p-3.5 rounded-lg border-2 border-[#1a4734] flex flex-wrap justify-between items-center gap-1">
                        <span class="font-bold text-base">Total Bayar:</span>
                        <span class="font-black text-3xl text-[#418b24]">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(buyProduct.price * buyQty)"></span>
                        </span>
                    </div>

                    <div class="pt-2 flex space-x-2.5">
                        <button type="submit" class="flex-1 bg-[#418b24] text-white min-h-[50px] py-3 rounded-lg font-black text-lg uppercase hover:opacity-90 active:scale-95 transition-all">
                            Bayar Sekarang (Lunas)
                        </button>
                        <button type="button" @click="buyNowModal = false" class="bg-gray-300 text-gray-800 px-5 min-h-[50px] py-3 rounded-lg font-bold text-base">
                            Batal
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
