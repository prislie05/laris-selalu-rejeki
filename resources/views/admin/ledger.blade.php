@extends('layouts.mobile', ['title' => 'Catatan Keuangan - Laris Selalu Rejeki', 'pageBg' => '#FFFFFF'])

@section('content')
<div class="py-2" x-data="{
    showCreateModal: false,
    showEditModal: false,
    ledgerType: 'expense',
    editData: { id: null, type: 'expense', category: 'Belanja Stok Barang', amount: 0, description: '', transaction_date: '{{ date('Y-m-d') }}' },

    openEdit(l) {
        this.editData = {
            id: l.id,
            type: l.type,
            category: l.category,
            amount: l.amount,
            description: l.description || '',
            transaction_date: l.transaction_date ? l.transaction_date.substring(0, 10) : '{{ date('Y-m-d') }}'
        };
        this.showEditModal = true;
    }
}">

    <!-- Top Action & Header (Besar & Jelas) -->
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-2.5 border-b-2 border-[#1a4734]">
        <div>
            <h2 class="font-black text-xl text-[#1a4734] uppercase tracking-wide flex items-center space-x-2">
                <svg class="w-6 h-6 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Catatan Keuangan</span>
            </h2>
            <span class="text-base text-gray-700 font-bold">Pembukuan Kas Toko</span>
        </div>
        <button 
            type="button" 
            @click="showCreateModal = true"
            class="bg-[#418b24] text-white px-5 min-h-[50px] py-3 rounded-xl font-black text-lg uppercase flex items-center space-x-2 hover:opacity-90 active:scale-95 transition-all shadow-sm"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>+ Catatan</span>
        </button>
    </div>

    <!-- Summary Box (Akumulasi Kas Real-Time - Angka Besar 24px - 28px) -->
    <div class="bg-white p-4 rounded-xl border-2 border-[#1a4734] mb-4 shadow-sm">
        <h3 class="font-black text-base text-[#1a4734] uppercase pb-2.5 border-b-2 border-gray-200 mb-2.5">
            Akumulasi Kas Toko Keseluruhan
        </h3>
        <div class="space-y-2 text-base font-semibold">
            <div class="flex flex-wrap justify-between items-center gap-1">
                <span class="text-gray-700">Total Uang Masuk:</span>
                <span class="font-black text-2xl text-[#418b24]">Rp {{ number_format($accumulatedIncome, 0, ',', '.') }}</span>
            </div>
            <div class="flex flex-wrap justify-between items-center gap-1">
                <span class="text-gray-700">Total Uang Keluar:</span>
                <span class="font-black text-2xl text-[#870903]">Rp {{ number_format($accumulatedExpense, 0, ',', '.') }}</span>
            </div>
            <div class="flex flex-wrap justify-between items-center gap-1 pt-2.5 border-t-2 border-gray-300 text-lg font-extrabold">
                <span class="text-[#1a4734]">Saldo Kas Toko:</span>
                <span class="font-black text-2xl {{ $currentBalance >= 0 ? 'text-[#1a4734]' : 'text-[#870903]' }}">
                    Rp {{ number_format($currentBalance, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Type Buttons -->
    <div class="flex flex-wrap gap-2 mb-4">
        <a 
            href="{{ route('admin.ledger') }}" 
            class="px-4 min-h-[44px] py-2 rounded-xl text-base font-bold border-2 border-[#1a4734] flex items-center justify-center {{ !$typeFilter ? 'bg-[#1a4734] text-[#f9dd9c]' : 'bg-white text-[#1a4734]' }}"
        >
            Semua
        </a>
        <a 
            href="{{ route('admin.ledger', ['type' => 'income']) }}" 
            class="px-4 min-h-[44px] py-2 rounded-xl text-base font-bold border-2 border-[#1a4734] flex items-center justify-center {{ $typeFilter === 'income' ? 'bg-[#418b24] text-white' : 'bg-white text-[#418b24]' }}"
        >
            Uang Masuk
        </a>
        <a 
            href="{{ route('admin.ledger', ['type' => 'expense']) }}" 
            class="px-4 min-h-[44px] py-2 rounded-xl text-base font-bold border-2 border-[#1a4734] flex items-center justify-center {{ $typeFilter === 'expense' ? 'bg-[#870903] text-white' : 'bg-white text-[#870903]' }}"
        >
            Uang Keluar
        </a>
    </div>

    <!-- Ledger Entries List -->
    <div class="space-y-3.5">
        @forelse($ledgers as $l)
            <div class="bg-white p-4 rounded-xl border-2 border-[#1a4734] shadow-sm">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="px-2.5 py-1 rounded-md font-black text-xs uppercase text-white {{ $l->type === 'income' ? 'bg-[#418b24]' : 'bg-[#870903]' }}">
                                {{ $l->type === 'income' ? 'Uang Masuk' : 'Uang Keluar' }}
                            </span>
                            <span class="text-sm text-gray-500 font-bold">{{ $l->transaction_date->format('d/m/Y') }}</span>
                        </div>
                        <h4 class="font-bold text-xl text-[#1a4734] mt-1 break-words leading-snug">{{ $l->category }}</h4>
                        @if($l->description)
                            <p class="text-base text-gray-700 mt-1 leading-relaxed break-words">{{ $l->description }}</p>
                        @endif
                    </div>

                    <div class="text-right flex-shrink-0">
                        <span class="text-2xl font-black block {{ $l->type === 'income' ? 'text-[#418b24]' : 'text-[#870903]' }}">
                            {{ $l->type === 'income' ? '+' : '-' }} Rp {{ number_format($l->amount, 0, ',', '.') }}
                        </span>
                        <span class="text-xs text-gray-400 block mt-1 font-semibold">Oleh: {{ $l->user?->name ?? 'Admin' }}</span>
                    </div>
                </div>

                <!-- Edit and Delete Actions -->
                <div class="mt-3.5 pt-3 border-t-2 border-gray-200 flex justify-end space-x-2.5">
                    <button 
                        type="button" 
                        @click="openEdit({{ json_encode($l) }})"
                        class="bg-[#1a4734] text-[#f9dd9c] px-4 min-h-[46px] py-2 rounded-lg font-bold text-base flex items-center space-x-1.5 hover:bg-[#143728] active:scale-95 transition-all"
                    >
                        <span>Ubah</span>
                    </button>

                    <form action="{{ route('admin.ledger.delete', $l->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan keuangan ini?');">
                        @csrf
                        <button 
                            type="submit" 
                            class="bg-[#870903] text-white px-4 min-h-[46px] py-2 rounded-lg font-bold text-base flex items-center space-x-1.5 hover:opacity-90 active:scale-95 transition-all"
                        >
                            <span>Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-xl border-2 border-[#1a4734] text-center my-6">
                <p class="font-extrabold text-xl text-[#1a4734]">Belum Ada Catatan Keuangan</p>
                <p class="text-base text-gray-700 mt-2">Tekan tombol + Catatan di atas untuk mencatat pemasukan atau pengeluaran.</p>
            </div>
        @endforelse
    </div>

    <!-- CREATE LEDGER MODAL -->
    <div 
        x-cloak 
        x-show="showCreateModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4 py-6"
        @click.self="showCreateModal = false"
    >
        <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[440px] rounded-xl p-5 text-[#1a4734] shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b-2 border-[#1a4734] mb-4">
                <h3 class="font-black text-xl text-[#1a4734] uppercase tracking-wide">Tambah Catatan Keuangan</h3>
                <button type="button" @click="showCreateModal = false" class="text-3xl font-black text-[#870903] leading-none p-1">&times;</button>
            </div>

            <form action="{{ route('admin.ledger.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1.5">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="border-2 rounded-xl p-3 flex items-center space-x-2 cursor-pointer bg-white" :class="ledgerType === 'income' ? 'border-[#418b24]' : 'border-gray-300'">
                            <input type="radio" name="type" value="income" x-model="ledgerType" class="w-5 h-5 text-[#418b24]">
                            <span class="font-black text-base text-[#418b24]">UANG MASUK</span>
                        </label>
                        <label class="border-2 rounded-xl p-3 flex items-center space-x-2 cursor-pointer bg-white" :class="ledgerType === 'expense' ? 'border-[#870903]' : 'border-gray-300'">
                            <input type="radio" name="type" value="expense" x-model="ledgerType" class="w-5 h-5 text-[#870903]">
                            <span class="font-black text-base text-[#870903]">UANG KELUAR</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Kategori Transaksi</label>
                    <template x-if="ledgerType === 'income'">
                        <select name="category" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-bold text-lg text-[#1a4734] focus:outline-none">
                            @foreach($categories['income'] as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </template>
                    <template x-if="ledgerType === 'expense'">
                        <select name="category" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-bold text-lg text-[#1a4734] focus:outline-none">
                            @foreach($categories['expense'] as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </template>
                </div>

                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Nominal Jumlah (Rp)</label>
                    <input type="number" name="amount" required min="1" step="100" placeholder="Contoh: 1500000" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-black text-2xl text-[#870903] focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Tanggal Transaksi</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-xl font-bold text-lg text-[#1a4734] focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Deskripsi / Keterangan</label>
                    <textarea name="description" rows="2" placeholder="Catatan detail pembukuan..." class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-xl text-base text-[#1a4734] focus:outline-none"></textarea>
                </div>

                <div class="pt-2 flex space-x-2.5">
                    <button type="submit" class="flex-1 bg-[#418b24] text-white min-h-[50px] py-3 rounded-xl font-black text-lg uppercase hover:opacity-90 active:scale-95 transition-all">
                        Simpan Catatan
                    </button>
                    <button type="button" @click="showCreateModal = false" class="bg-gray-300 text-gray-800 px-5 min-h-[50px] py-3 rounded-xl font-bold text-base">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT LEDGER MODAL -->
    <div 
        x-cloak 
        x-show="showEditModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4 py-6"
        @click.self="showEditModal = false"
    >
        <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[440px] rounded-xl p-5 text-[#1a4734] shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b-2 border-[#1a4734] mb-4">
                <h3 class="font-black text-xl text-[#1a4734] uppercase tracking-wide">Ubah Catatan Keuangan</h3>
                <button type="button" @click="showEditModal = false" class="text-3xl font-black text-[#870903] leading-none p-1">&times;</button>
            </div>

            <form :action="'{{ url('admin/ledger') }}/' + editData.id + '/update'" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1.5">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="border-2 rounded-xl p-3 flex items-center space-x-2 cursor-pointer bg-white" :class="editData.type === 'income' ? 'border-[#418b24]' : 'border-gray-300'">
                            <input type="radio" name="type" value="income" x-model="editData.type" class="w-5 h-5 text-[#418b24]">
                            <span class="font-black text-base text-[#418b24]">UANG MASUK</span>
                        </label>
                        <label class="border-2 rounded-xl p-3 flex items-center space-x-2 cursor-pointer bg-white" :class="editData.type === 'expense' ? 'border-[#870903]' : 'border-gray-300'">
                            <input type="radio" name="type" value="expense" x-model="editData.type" class="w-5 h-5 text-[#870903]">
                            <span class="font-black text-base text-[#870903]">UANG KELUAR</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Kategori Transaksi</label>
                    <input type="text" name="category" x-model="editData.category" required class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-bold text-lg text-[#1a4734]">
                </div>

                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Nominal Jumlah (Rp)</label>
                    <input type="number" name="amount" x-model="editData.amount" required min="1" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-3 rounded-xl font-black text-2xl text-[#870903] focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Tanggal Transaksi</label>
                    <input type="date" name="transaction_date" x-model="editData.transaction_date" required class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-xl font-bold text-lg text-[#1a4734] focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-base text-[#1a4734] mb-1">Deskripsi / Keterangan</label>
                    <textarea name="description" x-model="editData.description" rows="2" class="w-full bg-white border-2 border-[#1a4734] px-3.5 py-2.5 rounded-xl text-base text-[#1a4734] focus:outline-none"></textarea>
                </div>

                <div class="pt-2 flex space-x-2.5">
                    <button type="submit" class="flex-1 bg-[#418b24] text-white min-h-[50px] py-3 rounded-xl font-black text-lg uppercase hover:opacity-90 active:scale-95 transition-all">
                        Perbarui Catatan
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
