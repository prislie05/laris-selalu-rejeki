@extends('layouts.mobile', ['title' => 'Catatan Keuangan - Toko Lily'])

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

    <!-- Top Action & Header -->
    <div class="flex items-center justify-between mb-3 pb-2 border-b-2 border-[#1a4734]">
        <div>
            <h2 class="font-black text-sm text-[#1a4734] uppercase tracking-wide flex items-center space-x-1.5">
                <svg class="w-5 h-5 text-[#1a4734]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Catatan Keuangan</span>
            </h2>
            <span class="text-[11px] text-gray-600">Pembukuan Uang Masuk & Keluar</span>
        </div>
        <button 
            type="button" 
            @click="showCreateModal = true"
            class="bg-[#418b24] text-white px-3 py-1.5 rounded font-black text-xs uppercase flex items-center space-x-1 hover:opacity-90"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Catatan</span>
        </button>
    </div>

    <!-- Summary Box (Akumulasi Real-Time) -->
    <div class="bg-white p-3.5 rounded-lg border-2 border-[#1a4734] mb-3">
        <h3 class="font-black text-xs text-[#1a4734] uppercase pb-2 border-b border-gray-200 mb-2">
            Akumulasi Kas Toko Keseluruhan
        </h3>
        <div class="space-y-1.5 text-xs font-semibold">
            <div class="flex justify-between">
                <span class="text-gray-600">Total Uang Masuk (Pemasukan):</span>
                <span class="font-black text-[#418b24]">Rp {{ number_format($accumulatedIncome, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Total Uang Keluar (Pengeluaran):</span>
                <span class="font-black text-[#870903]">Rp {{ number_format($accumulatedExpense, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between pt-1.5 border-t border-gray-300 text-sm font-extrabold">
                <span class="text-[#1a4734]">Saldo Kas Saat Ini:</span>
                <span class="font-black {{ $currentBalance >= 0 ? 'text-[#1a4734]' : 'text-[#870903]' }}">
                    Rp {{ number_format($currentBalance, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Type -->
    <div class="flex gap-2 mb-3">
        <a 
            href="{{ route('admin.ledger') }}" 
            class="px-3 py-1 rounded text-xs font-bold border border-[#1a4734] {{ !$typeFilter ? 'bg-[#1a4734] text-[#f9dd9c]' : 'bg-white text-[#1a4734]' }}"
        >
            Semua
        </a>
        <a 
            href="{{ route('admin.ledger', ['type' => 'income']) }}" 
            class="px-3 py-1 rounded text-xs font-bold border border-[#1a4734] {{ $typeFilter === 'income' ? 'bg-[#418b24] text-white' : 'bg-white text-[#418b24]' }}"
        >
            Uang Masuk
        </a>
        <a 
            href="{{ route('admin.ledger', ['type' => 'expense']) }}" 
            class="px-3 py-1 rounded text-xs font-bold border border-[#1a4734] {{ $typeFilter === 'expense' ? 'bg-[#870903] text-white' : 'bg-white text-[#870903]' }}"
        >
            Uang Keluar
        </a>
    </div>

    <!-- Ledger Entries List -->
    <div class="space-y-2.5">
        @forelse($ledgers as $l)
            <div class="bg-white p-3 rounded-lg border-2 border-[#1a4734]">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center space-x-1.5 mb-0.5">
                            <span class="px-2 py-0.5 rounded font-black text-[10px] uppercase text-white {{ $l->type === 'income' ? 'bg-[#418b24]' : 'bg-[#870903]' }}">
                                {{ $l->type === 'income' ? 'Uang Masuk' : 'Uang Keluar' }}
                            </span>
                            <span class="text-[10px] text-gray-500 font-semibold">{{ $l->transaction_date->format('d/m/Y') }}</span>
                        </div>
                        <h4 class="font-extrabold text-xs text-[#1a4734] mt-1">{{ $l->category }}</h4>
                        @if($l->description)
                            <p class="text-[11px] text-gray-600 mt-0.5">{{ $l->description }}</p>
                        @endif
                    </div>

                    <div class="text-right flex-shrink-0">
                        <span class="text-xs font-black block {{ $l->type === 'income' ? 'text-[#418b24]' : 'text-[#870903]' }}">
                            {{ $l->type === 'income' ? '+' : '-' }} Rp {{ number_format($l->amount, 0, ',', '.') }}
                        </span>
                        <span class="text-[9px] text-gray-400 block mt-0.5">Oleh: {{ $l->user?->name ?? 'Admin' }}</span>
                    </div>
                </div>

                <!-- Edit and Delete Actions -->
                <div class="mt-2.5 pt-2 border-t border-gray-200 flex justify-end space-x-2">
                    <button 
                        type="button" 
                        @click="openEdit({{ json_encode($l) }})"
                        class="bg-[#1a4734] text-[#f9dd9c] px-3 py-1 rounded font-bold text-xs flex items-center space-x-1 hover:bg-[#143728]"
                    >
                        <span>Ubah</span>
                    </button>

                    <form action="{{ route('admin.ledger.delete', $l->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan keuangan ini?');">
                        @csrf
                        <button 
                            type="submit" 
                            class="bg-[#870903] text-white px-3 py-1 rounded font-bold text-xs flex items-center space-x-1 hover:opacity-90"
                        >
                            <span>Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white p-6 rounded-lg border-2 border-[#1a4734] text-center">
                <p class="font-bold text-xs text-[#1a4734]">Belum ada catatan keuangan.</p>
            </div>
        @endforelse
    </div>

    <!-- CREATE LEDGER MODAL -->
    <div 
        x-cloak 
        x-show="showCreateModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        @click.self="showCreateModal = false"
    >
        <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[390px] rounded p-4 text-[#1a4734]">
            <div class="flex items-center justify-between pb-2 border-b-2 border-[#1a4734] mb-3">
                <h3 class="font-black text-sm text-[#1a4734] uppercase tracking-wide">Tambah Catatan Keuangan</h3>
                <button type="button" @click="showCreateModal = false" class="text-xl font-black text-[#870903]">&times;</button>
            </div>

            <form action="{{ route('admin.ledger.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-[#1a4734] mb-1">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="border-2 rounded p-2 flex items-center space-x-1.5 cursor-pointer bg-white" :class="ledgerType === 'income' ? 'border-[#418b24]' : 'border-gray-300'">
                            <input type="radio" name="type" value="income" x-model="ledgerType">
                            <span class="font-bold text-[#418b24]">UANG MASUK</span>
                        </label>
                        <label class="border-2 rounded p-2 flex items-center space-x-1.5 cursor-pointer bg-white" :class="ledgerType === 'expense' ? 'border-[#870903]' : 'border-gray-300'">
                            <input type="radio" name="type" value="expense" x-model="ledgerType">
                            <span class="font-bold text-[#870903]">UANG KELUAR</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#1a4734] mb-1">Kategori Transaksi</label>
                    <template x-if="ledgerType === 'income'">
                        <select name="category" class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded font-bold text-[#1a4734]">
                            @foreach($categories['income'] as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </template>
                    <template x-if="ledgerType === 'expense'">
                        <select name="category" class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded font-bold text-[#1a4734]">
                            @foreach($categories['expense'] as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </template>
                </div>

                <div>
                    <label class="block font-bold text-[#1a4734] mb-1">Nominal Jumlah (Rp)</label>
                    <input type="number" name="amount" required min="1" step="100" placeholder="Contoh: 1500000" class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded font-black text-sm text-[#870903] focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-[#1a4734] mb-1">Tanggal Transaksi</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded font-semibold text-[#1a4734] focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-[#1a4734] mb-1">Deskripsi / Keterangan</label>
                    <textarea name="description" rows="2" placeholder="Catatan detail pembukuan..." class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded text-[#1a4734] focus:outline-none"></textarea>
                </div>

                <div class="pt-2 flex space-x-2">
                    <button type="submit" class="flex-1 bg-[#418b24] text-white py-2 rounded font-black text-xs uppercase hover:opacity-90">
                        Simpan Catatan
                    </button>
                    <button type="button" @click="showCreateModal = false" class="bg-gray-300 text-gray-800 px-3 py-2 rounded font-bold text-xs">
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
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        @click.self="showEditModal = false"
    >
        <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[390px] rounded p-4 text-[#1a4734]">
            <div class="flex items-center justify-between pb-2 border-b-2 border-[#1a4734] mb-3">
                <h3 class="font-black text-sm text-[#1a4734] uppercase tracking-wide">Ubah Catatan Keuangan</h3>
                <button type="button" @click="showEditModal = false" class="text-xl font-black text-[#870903]">&times;</button>
            </div>

            <form :action="'{{ url('admin/ledger') }}/' + editData.id + '/update'" method="POST" class="space-y-3 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-[#1a4734] mb-1">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="border-2 rounded p-2 flex items-center space-x-1.5 cursor-pointer bg-white" :class="editData.type === 'income' ? 'border-[#418b24]' : 'border-gray-300'">
                            <input type="radio" name="type" value="income" x-model="editData.type">
                            <span class="font-bold text-[#418b24]">UANG MASUK</span>
                        </label>
                        <label class="border-2 rounded p-2 flex items-center space-x-1.5 cursor-pointer bg-white" :class="editData.type === 'expense' ? 'border-[#870903]' : 'border-gray-300'">
                            <input type="radio" name="type" value="expense" x-model="editData.type">
                            <span class="font-bold text-[#870903]">UANG KELUAR</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#1a4734] mb-1">Kategori Transaksi</label>
                    <input type="text" name="category" x-model="editData.category" required class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded font-bold text-[#1a4734]">
                </div>

                <div>
                    <label class="block font-bold text-[#1a4734] mb-1">Nominal Jumlah (Rp)</label>
                    <input type="number" name="amount" x-model="editData.amount" required min="1" class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded font-black text-sm text-[#870903] focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-[#1a4734] mb-1">Tanggal Transaksi</label>
                    <input type="date" name="transaction_date" x-model="editData.transaction_date" required class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded font-semibold text-[#1a4734] focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-[#1a4734] mb-1">Deskripsi / Keterangan</label>
                    <textarea name="description" x-model="editData.description" rows="2" class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded text-[#1a4734] focus:outline-none"></textarea>
                </div>

                <div class="pt-2 flex space-x-2">
                    <button type="submit" class="flex-1 bg-[#418b24] text-white py-2 rounded font-black text-xs uppercase hover:opacity-90">
                        Perbarui Catatan
                    </button>
                    <button type="button" @click="showEditModal = false" class="bg-gray-300 text-gray-800 px-3 py-2 rounded font-bold text-xs">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
