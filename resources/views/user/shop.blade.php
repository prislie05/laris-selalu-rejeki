@extends('layouts.mobile', ['title' => 'Belanja - Toko Lily'])

@section('content')
<div class="py-2" x-data="{ buyNowModal: false, buyProduct: null, buyQty: 1 }">

    <!-- Search Bar -->
    <div class="mb-3">
        <form action="{{ route('user.shop') }}" method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari barang atau kategori..." 
                    class="w-full bg-white border-2 border-[#1a4734] pl-9 pr-3 py-2 rounded-lg text-xs font-semibold text-[#1a4734] placeholder-gray-500 focus:outline-none"
                >
                <svg class="w-4 h-4 text-[#1a4734] absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button type="submit" class="bg-[#1a4734] text-[#f9dd9c] px-3.5 py-2 rounded-lg font-bold text-xs">
                Cari
            </button>
            @if($search)
                <a href="{{ route('user.shop') }}" class="bg-[#870903] text-white px-2.5 py-2 rounded-lg font-bold text-xs flex items-center justify-center" title="Reset Pencarian">
                    &times;
                </a>
            @endif
        </form>
    </div>

    <!-- Category Filter Pills -->
    <div class="flex gap-1.5 overflow-x-auto pb-2 mb-3 no-scrollbar">
        <a 
            href="{{ route('user.shop', array_merge(request()->except('category'), ['category' => 'Semua'])) }}" 
            class="px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap border border-[#1a4734] {{ $selectedCategory === 'Semua' ? 'bg-[#1a4734] text-[#f9dd9c]' : 'bg-white text-[#1a4734]' }}"
        >
            Semua
        </a>
        @foreach($categories as $cat)
            <a 
                href="{{ route('user.shop', array_merge(request()->except('category'), ['category' => $cat])) }}" 
                class="px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap border border-[#1a4734] {{ $selectedCategory === $cat ? 'bg-[#1a4734] text-[#f9dd9c]' : 'bg-white text-[#1a4734]' }}"
            >
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Product Grid Catalog -->
    <div class="space-y-3">
        @forelse($products as $prod)
            <div class="bg-white rounded-lg border-2 border-[#1a4734] p-3 flex flex-col justify-between">
                <div class="flex space-x-3">
                    <!-- Product Image -->
                    <div class="w-20 h-20 bg-[#f9dd9c]/40 rounded border border-[#1a4734] overflow-hidden flex-shrink-0 flex items-center justify-center">
                        @if($prod->image_url)
                            <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="font-extrabold text-[#1a4734] text-xs">TOKO LILY</span>
                        @endif
                    </div>

                    <!-- Product Information -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-[#f9dd9c] text-[#1a4734] uppercase border border-[#1a4734]/30">
                                {{ $prod->category }}
                            </span>
                            @if($prod->stock <= 0)
                                <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-[#870903] text-white">
                                    STOK HABIS
                                </span>
                            @elseif($prod->stock <= 5)
                                <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-[#e90c00] text-white">
                                    Sisa {{ $prod->stock }}
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-gray-500">
                                    Stok: {{ $prod->stock }}
                                </span>
                            @endif
                        </div>

                        <h3 class="font-extrabold text-sm text-[#1a4734] leading-tight truncate">
                            {{ $prod->name }}
                        </h3>

                        <p class="text-[11px] text-gray-600 line-clamp-1 mt-0.5">
                            {{ $prod->description ?? 'Barang berkualitas Toko Lily' }}
                        </p>

                        <div class="mt-1 font-black text-sm text-[#870903]">
                            Rp {{ number_format($prod->price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-2 mt-3 pt-2 border-t border-gray-200">
                    <!-- Tambah ke Keranjang -->
                    <form action="{{ route('user.cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $prod->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button 
                            type="submit" 
                            {{ $prod->stock <= 0 ? 'disabled' : '' }}
                            class="w-full bg-[#f9dd9c] text-[#1a4734] border-2 border-[#1a4734] py-1.5 rounded font-black text-[11px] flex items-center justify-center space-x-1 hover:bg-[#ebd090] disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
                        class="w-full bg-[#418b24] text-white py-1.5 rounded font-black text-[11px] flex items-center justify-center space-x-1 hover:opacity-90 disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <span>Beli Sekarang</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white p-6 rounded-lg border-2 border-[#1a4734] text-center">
                <p class="font-bold text-sm text-[#870903]">Tidak ada barang yang cocok.</p>
                <p class="text-xs text-gray-600 mt-1">Coba cari dengan kata kunci lain atau pilih kategori Semua.</p>
            </div>
        @endforelse
    </div>

    <!-- Buy Now Modal -->
    <div 
        x-cloak 
        x-show="buyNowModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        @click.self="buyNowModal = false"
    >
        <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[390px] rounded p-4 text-[#1a4734]">
            <div class="flex items-center justify-between pb-2 border-b-2 border-[#1a4734] mb-3">
                <h3 class="font-black text-sm text-[#1a4734] uppercase tracking-wide">Beli Langsung</h3>
                <button type="button" @click="buyNowModal = false" class="text-xl font-black text-[#870903]">&times;</button>
            </div>

            <template x-if="buyProduct">
                <form action="{{ route('user.checkout') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="product_id" :value="buyProduct.id">

                    <div class="bg-white p-2.5 rounded border border-[#1a4734]">
                        <p class="font-extrabold text-sm" x-text="buyProduct.name"></p>
                        <p class="text-xs font-black text-[#870903] mt-0.5">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(buyProduct.price)"></span>
                        </p>
                        <p class="text-[11px] text-gray-500">Stok tersedia: <span x-text="buyProduct.stock"></span></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1a4734] mb-1">Jumlah Beli</label>
                        <div class="flex items-center space-x-2">
                            <button 
                                type="button" 
                                @click="if(buyQty > 1) buyQty--" 
                                class="w-8 h-8 bg-[#1a4734] text-white rounded font-black text-base flex items-center justify-center"
                            >-</button>
                            <input 
                                type="number" 
                                name="quantity" 
                                x-model="buyQty" 
                                min="1" 
                                :max="buyProduct.stock" 
                                class="w-16 text-center bg-white border border-[#1a4734] py-1 font-bold text-sm rounded focus:outline-none"
                            >
                            <button 
                                type="button" 
                                @click="if(buyQty < buyProduct.stock) buyQty++" 
                                class="w-8 h-8 bg-[#1a4734] text-white rounded font-black text-base flex items-center justify-center"
                            >+</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1a4734] mb-1">Metode Pembayaran</label>
                        <select name="payment_method" class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded text-xs font-bold text-[#1a4734] focus:outline-none">
                            <option value="qris">QRIS (Otomatis / Instan)</option>
                            <option value="cash">Tunai (Bayar di Tempat)</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>

                    <div class="bg-white p-2.5 rounded border border-[#1a4734] flex justify-between items-center text-xs">
                        <span class="font-bold">Total Pembayaran:</span>
                        <span class="font-black text-base text-[#418b24]">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(buyProduct.price * buyQty)"></span>
                        </span>
                    </div>

                    <div class="pt-2 flex space-x-2">
                        <button type="submit" class="flex-1 bg-[#418b24] text-white py-2 rounded font-black text-xs uppercase hover:opacity-90">
                            Bayar Sekarang (Lunas)
                        </button>
                        <button type="button" @click="buyNowModal = false" class="bg-gray-300 text-gray-800 px-3 py-2 rounded font-bold text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
