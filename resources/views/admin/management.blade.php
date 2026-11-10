@extends('layouts.mobile', ['title' => 'Admin & Manajemen - Toko Lily', 'pageBg' => '#FFFFFF'])

@section('content')
<div class="py-2" x-data="{
    currentTab: 'accounts', // 'accounts', 'warnings'
    roleModal: false,
    selectedUser: null,

    openRoleModal(user) {
        this.selectedUser = user;
        this.roleModal = true;
    }
}">

    <!-- Sub-tab Navigation (Akun User & Stok Kritis: Font & Spacing Identik, Ramah Lansia) -->
    <div class="grid grid-cols-2 gap-2 bg-[#1a4734] p-1.5 rounded-xl mb-4">
        <button 
            type="button" 
            @click="currentTab = 'accounts'"
            :class="currentTab === 'accounts' ? 'bg-[#f9dd9c] text-[#1a4734]' : 'text-[#f9dd9c] hover:text-white'"
            class="min-h-[50px] px-4 py-3 text-lg font-black rounded-lg leading-none transition-all flex items-center justify-center space-x-2 tracking-normal active:scale-95"
        >
            <span>Akun User</span>
        </button>
        <button 
            type="button" 
            @click="currentTab = 'warnings'"
            :class="currentTab === 'warnings' ? 'bg-[#f9dd9c] text-[#1a4734]' : 'text-[#f9dd9c] hover:text-white'"
            class="min-h-[50px] px-4 py-3 text-lg font-black rounded-lg leading-none transition-all flex items-center justify-center space-x-2 tracking-normal active:scale-95"
        >
            <span>Stok Kritis</span>
            @if($lowStockProducts->count() > 0)
                <span class="inline-flex items-center justify-center min-w-6 h-6 px-1.5 rounded-full text-xs font-black bg-[#e90c00] text-white leading-none border border-white">
                    {{ $lowStockProducts->count() }}
                </span>
            @endif
        </button>
    </div>

    <!-- SUB-TAB 1: MANAJEMEN AKUN REAL-TIME -->
    <div x-show="currentTab === 'accounts'" class="space-y-4">
        <div class="flex items-center justify-between pb-2 border-b-2 border-[#1a4734]">
            <h3 class="font-black text-lg text-[#1a4734] uppercase tracking-normal">Daftar Akun Terdaftar</h3>
            <span class="text-base font-extrabold text-gray-700">{{ $users->count() }} Akun</span>
        </div>

        @foreach($users as $u)
            <div class="bg-white p-4 rounded-xl border-2 border-[#1a4734] {{ $u->is_banned ? 'bg-red-50/80 border-[#870903]' : '' }} shadow-sm">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                            <span class="px-3 py-1 rounded-md text-xs font-black uppercase text-white {{ $u->role === 'admin' ? 'bg-[#870903]' : ($u->role === 'seller' ? 'bg-[#e90c00]' : 'bg-[#418b24]') }}">
                                {{ $u->role }}
                            </span>
                            @if($u->is_banned)
                                <span class="px-3 py-1 rounded-md text-xs font-black uppercase bg-[#870903] text-white">
                                    BANNED
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-md text-xs font-bold uppercase bg-green-100 text-[#418b24]">
                                    Aktif
                                </span>
                            @endif
                        </div>
                        <h4 class="font-bold text-xl text-[#1a4734] break-words">{{ $u->name }}</h4>
                        <p class="text-base text-gray-700 break-words mt-0.5">{{ $u->email }}</p>
                        @if($u->phone)
                            <p class="text-sm text-gray-600 font-semibold mt-0.5">Telp: {{ $u->phone }}</p>
                        @endif
                    </div>

                    <div class="text-right text-xs text-gray-500 font-bold flex-shrink-0">
                        ID: #{{ $u->id }}
                    </div>
                </div>

                <!-- Admin Action Buttons for Account Management: min-h-[48px] text-base font-bold -->
                @if($u->id !== Auth::id())
                    <div class="mt-3.5 pt-3 border-t-2 border-gray-200 grid grid-cols-3 gap-2 text-center">
                        
                        <!-- 1. Ubah Role -->
                        <button 
                            type="button" 
                            @click="openRoleModal({{ json_encode($u) }})"
                            class="bg-[#1a4734] text-[#f9dd9c] min-h-[48px] py-2.5 px-2 rounded-lg font-black text-sm flex items-center justify-center space-x-1 hover:bg-[#143728] active:scale-95 transition-all"
                        >
                            <span>Ubah Role</span>
                        </button>

                        <!-- 2. Ban / Unban -->
                        <form action="{{ route('admin.users.ban', $u->id) }}" method="POST">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full min-h-[48px] py-2.5 px-2 rounded-lg font-black text-sm text-white uppercase {{ $u->is_banned ? 'bg-[#418b24]' : 'bg-[#e90c00]' }} hover:opacity-90 active:scale-95 transition-all"
                            >
                                {{ $u->is_banned ? 'Buka Ban' : 'Ban Akun' }}
                            </button>
                        </form>

                        <!-- 3. Hapus Akun -->
                        <form action="{{ route('admin.users.delete', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }} secara permanen?');">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full bg-[#870903] text-white min-h-[48px] py-2.5 px-2 rounded-lg font-black text-sm uppercase hover:opacity-90 active:scale-95 transition-all"
                            >
                                Hapus
                            </button>
                        </form>
                    </div>
                @else
                    <div class="mt-3 pt-2 border-t-2 border-gray-200 text-center">
                        <span class="text-sm italic text-gray-500 font-bold">(Akun Anda Sendiri)</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- SUB-TAB 2: PERINGATAN STOK KRITIS / MENIPIS -->
    <div x-show="currentTab === 'warnings'" class="space-y-3.5">
        <div class="flex items-center justify-between pb-2 border-b-2 border-[#870903]">
            <h3 class="font-black text-lg text-[#870903] uppercase tracking-normal">Peringatan Barang Stok Kritis (<= 5)</h3>
            <span class="text-sm font-black text-[#870903]">{{ $lowStockProducts->count() }} Barang Perlu Restock</span>
        </div>

        @forelse($lowStockProducts as $low)
            <div class="bg-white p-4 rounded-xl border-2 border-[#870903] flex flex-wrap items-center justify-between gap-2 shadow-sm">
                <div>
                    <h4 class="font-bold text-xl text-[#1a4734] break-words">{{ $low->name }}</h4>
                    <span class="text-sm text-gray-600 block font-semibold">{{ $low->category }}</span>
                    <span class="font-black text-2xl text-[#870903] mt-1 block">Rp {{ number_format($low->price, 0, ',', '.') }}</span>
                </div>

                <div class="text-right flex-shrink-0">
                    <span class="px-4 py-2 rounded-lg text-base font-black uppercase text-white {{ $low->stock <= 0 ? 'bg-[#870903]' : 'bg-[#e90c00]' }} inline-block">
                        {{ $low->stock <= 0 ? 'HABIS (0)' : 'Sisa ' . $low->stock }}
                    </span>
                    <a href="{{ route('seller.products') }}" class="block text-sm font-bold text-[#1a4734] hover:underline mt-2">
                        Restock Sekarang &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-xl border-2 border-[#1a4734] text-center my-6">
                <p class="font-black text-2xl text-[#418b24]">Semua Stok Barang Aman & Tercukupi!</p>
            </div>
        @endforelse
    </div>

    <!-- UBAH ROLE MODAL (Besar & Jelas) -->
    <div 
        x-cloak 
        x-show="roleModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4 py-6"
        @click.self="roleModal = false"
    >
        <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[420px] rounded-xl p-5 text-[#1a4734] shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b-2 border-[#1a4734] mb-4">
                <h3 class="font-black text-xl text-[#1a4734] uppercase tracking-wide">Ubah Hak Akses / Role</h3>
                <button type="button" @click="roleModal = false" class="text-3xl font-black text-[#870903] leading-none p-1">&times;</button>
            </div>

            <template x-if="selectedUser">
                <form :action="'{{ url('admin/users') }}/' + selectedUser.id + '/role'" method="POST" class="space-y-4">
                    @csrf
                    <p class="font-bold text-lg text-gray-800">
                        Pengguna: <span class="font-black text-[#1a4734]" x-text="selectedUser.name"></span>
                    </p>

                    <div>
                        <label class="block font-bold text-base text-[#1a4734] mb-1.5">Pilih Role Baru</label>
                        <select name="role" x-model="selectedUser.role" class="w-full bg-white border-2 border-[#1a4734] px-4 py-3 rounded-xl font-bold text-lg text-[#1a4734] focus:outline-none">
                            <option value="user">User (Pelanggan Biasa)</option>
                            <option value="seller">Seller (Kasir POS & Toko)</option>
                            <option value="admin">Admin (Akses Penuh Keuangan & Sistem)</option>
                        </select>
                    </div>

                    <div class="pt-2 flex space-x-2.5">
                        <button type="submit" class="flex-1 bg-[#418b24] text-white min-h-[50px] py-3 rounded-xl font-black text-lg uppercase hover:opacity-90 active:scale-95 transition-all">
                            Simpan Role
                        </button>
                        <button type="button" @click="roleModal = false" class="bg-gray-300 text-gray-800 px-5 min-h-[50px] py-3 rounded-xl font-bold text-base">
                            Batal
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
