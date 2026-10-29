@extends('layouts.mobile', ['title' => 'Manajemen Barang - Toko Lily'])

@section('content')
<div class="py-2" x-data="{
    showCreateModal: false,
    showEditModal: false,
    editProduct: { id: null, name: '', category: 'Sembako', price: 0, stock: 0, description: '', image_url: '' },

    openEdit(prod) {
        this.editProduct = {
            id: prod.id,
            name: prod.name,
            category: prod.category,
            price: prod.price,
            stock: prod.stock,
            description: prod.description || '',
            image_url: prod.image_url || ''
        };
        this.showEditModal = true;
    }
}">

    <!-- Top Action: Add Product Button (Besar & Jelas) -->
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-2.5 border-b-2 border-[#1a4734]">
        <div>
            <h2 class="font-black text-xl text-[#1a4734] uppercase tracking-wide flex items-center space-x-2">
                <svg class="w-6 h-6 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Barang</span>
            </h2>
            <span class="text-base text-gray-700 font-bold">Total: {{ $products->count() }} barang etalase</span>
        </div>
        <button 
            type="button" 
            @click="showCreateModal = true"
            class="bg-[#418b24] text-white px-5 min-h-[50px] py-3 rounded-xl font-black text-lg uppercase flex items-center space-x-2 hover:opacity-90 active:scale-95 transition-all shadow-sm"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>+ Barang</span>
        </button>
    </div>

    <!-- Products Management List -->
    <div class="space-y-3.5">
        @foreach($products as $prod)
            <div class="bg-white p-4 rounded-xl border-2 border-[#1a4734] shadow-sm">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex space-x-3.5 min-w-0">
                        <div class="w-16 h-16 bg-[#f9dd9c]/40 rounded-lg border border-[#1a4734] overflow-hidden flex-shrink-0 flex items-center justify-center">
                            @if($prod->image_url)
                                <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="font-black text-[#1a4734] text-xs">TL</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-black px-2.5 py-0.5 rounded bg-gray-100 text-gray-700 uppercase border border-gray-300">
                                {{ $prod->category }}
                            </span>
                            <h4 class="font-bold text-xl text-[#1a4734] break-words mt-1 leading-snug">{{ $prod->name }}</h4>
                            <p class="font-black text-2xl text-[#870903] mt-1">
                                Rp {{ number_format($prod->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right flex-shrink-0 pl-1">
                        <span class="font-black text-base px-3 py-1.5 rounded-lg {{ $prod->stock <= 0 ? 'bg-[#870903] text-white' : ($prod->stock <= 5 ? 'bg-[#e90c00] text-white' : 'bg-[#418b24] text-white') }}">
                            Stok: {{ $prod->stock }}
                        </span>
                    </div>
                </div>

                <!-- Update & Delete Buttons: min-h-[48px] text-base font-bold -->
                <div class="mt-3.5 pt-3 border-t-2 border-gray-200 flex justify-end space-x-2.5">
                    <button 
                        type="button" 
                        @click="openEdit({{ json_encode($prod) }})"
                        class="bg-[#1a4734] text-[#f9dd9c] px-5 min-h-[48px] py-2.5 rounded-lg font-black text-base flex items-center space-x-1.5 hover:bg-[#143728] active:scale-95 transition-all"
                    >
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        <span>Ubah</span>
                    </button>

                    <form action="{{ route('seller.products.delete', $prod->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini dari etalase?');">
                        @csrf
                        <button 
                            type="submit" 
                            class="bg-[#870903] text-white px-5 min-h-[48px] py-2.5 rounded-lg font-black text-base flex items-center space-x-1.5 hover:opacity-90 active:scale-95 transition-all"
                        >
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- CREATE PRODUCT MODAL -->
    <div 
        x-cloak 
        x-show="showCreateModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4 py-6"
        @click.self="showCreateModal = false"
    >
        <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[440px] rounded-xl p-5 text-[#1a4734] max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b-2 border-[#1a4734] mb-4">
                <h3 class="font-black text-xl text-[#1a4734] uppercase tracking-wide">Tambah Barang Baru</h3>
                <button type="button" @click="showCreateModal = false" class="text-3xl font-black text-[#870903] leading-none p-1">&times;</button>
            </div>

            <form action="{{ route('seller.products.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Nama Barang</label>
                    <input type="text" name="name" required placeholder="Contoh: Beras Ramos 5kg" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-bold text-lg text-[#1a4734] focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Kategori</label>
                    <select name="category" required class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-bold text-lg text-[#1a4734] focus:outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-base text-[#1a4734] mb-1">Harga (Rp)</label>
                        <input type="number" name="price" required min="100" step="100" placeholder="75000" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-black text-xl text-[#870903] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-base text-[#1a4734] mb-1">Jumlah Stok</label>
                        <input type="number" name="stock" required min="0" placeholder="10" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-black text-xl text-[#1a4734] focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">URL Foto Produk</label>
                    <input type="url" name="image_url" placeholder="https://..." class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-xl text-base text-[#1a4734] focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Keterangan isi barang..." class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-xl text-base text-[#1a4734] focus:outline-none"></textarea>
                </div>

                <div class="pt-2 flex space-x-2.5">
                    <button type="submit" class="flex-1 bg-[#418b24] text-white min-h-[50px] py-3 rounded-xl font-black text-lg uppercase hover:opacity-90 active:scale-95 transition-all">
                        Simpan Barang
                    </button>
                    <button type="button" @click="showCreateModal = false" class="bg-gray-300 text-gray-800 px-5 min-h-[50px] py-3 rounded-xl font-bold text-base">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT PRODUCT MODAL -->
    <div 
        x-cloak 
        x-show="showEditModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4 py-6"
        @click.self="showEditModal = false"
    >
        <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[440px] rounded-xl p-5 text-[#1a4734] max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b-2 border-[#1a4734] mb-4">
                <h3 class="font-black text-xl text-[#1a4734] uppercase tracking-wide">Perbarui Data Barang</h3>
                <button type="button" @click="showEditModal = false" class="text-3xl font-black text-[#870903] leading-none p-1">&times;</button>
            </div>

            <form :action="'{{ url('seller/products') }}/' + editProduct.id + '/update'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Nama Barang</label>
                    <input type="text" name="name" x-model="editProduct.name" required class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-bold text-lg text-[#1a4734] focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Kategori</label>
                    <select name="category" x-model="editProduct.category" required class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-bold text-lg text-[#1a4734] focus:outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-base text-[#1a4734] mb-1">Harga (Rp)</label>
                        <input type="number" name="price" x-model="editProduct.price" required min="100" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-black text-xl text-[#870903] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-base text-[#1a4734] mb-1">Jumlah Stok</label>
                        <input type="number" name="stock" x-model="editProduct.stock" required min="0" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-black text-xl text-[#1a4734] focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">URL Foto Produk</label>
                    <input type="url" name="image_url" x-model="editProduct.image_url" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-xl text-base text-[#1a4734] focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Deskripsi Singkat</label>
                    <textarea name="description" x-model="editProduct.description" rows="2" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-xl text-base text-[#1a4734] focus:outline-none"></textarea>
                </div>

                <div class="pt-2 flex space-x-2.5">
                    <button type="submit" class="flex-1 bg-[#418b24] text-white min-h-[50px] py-3 rounded-xl font-black text-lg uppercase hover:opacity-90 active:scale-95 transition-all">
                        Perbarui Barang
                    </button>
                    <button type="button" @click="showEditModal = false" class="bg-gray-300 text-gray-800 px-5 min-h-[50px] py-3 rounded-xl font-bold text-base">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
