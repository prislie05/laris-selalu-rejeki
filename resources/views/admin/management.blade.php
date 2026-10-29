@extends('layouts.mobile', ['title' => 'Admin & Manajemen - Toko Lily'])

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

    <!-- Sub-tab Navigation (2 Tab: Akun User & Stok Kritis) -->
    <div class="grid grid-cols-2 gap-1.5 bg-[#1a4734] p-1.5 rounded-lg mb-3">
        <button 
            type="button" 
            @click="currentTab = 'accounts'"
            :class="currentTab === 'accounts' ? 'bg-[#f9dd9c] text-[#1a4734]' : 'text-[#f9dd9c] hover:text-white'"
            class="h-9 px-3 py-2 text-xs font-extrabold rounded leading-none transition-colors flex items-center justify-center space-x-1.5 tracking-normal"
        >
            <span>Akun User</span>
        </button>
        <button 
            type="button" 
            @click="currentTab = 'warnings'"
            :class="currentTab === 'warnings' ? 'bg-[#f9dd9c] text-[#1a4734]' : 'text-[#f9dd9c] hover:text-white'"
            class="h-9 px-3 py-2 text-xs font-extrabold rounded leading-none transition-colors flex items-center justify-center space-x-1.5 tracking-normal"
        >
            <span>Stok Kritis</span>
            @if($lowStockProducts->count() > 0)
                <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-[10px] font-black bg-[#e90c00] text-white leading-none">
                    {{ $lowStockProducts->count() }}
                </span>
            @endif
        </button>
    </div>

    <!-- SUB-TAB 1: MANAJEMEN AKUN REAL-TIME -->
    <div x-show="currentTab === 'accounts'" class="space-y-3">
        <div class="flex items-center justify-between pb-1 border-b border-[#1a4734]">
            <h3 class="font-black text-xs text-[#1a4734] uppercase tracking-normal">Daftar Akun Pengguna</h3>
            <span class="text-[10px] font-bold text-gray-500">{{ $users->count() }} Terdaftar</span>
        </div>

        @foreach($users as $u)
            <div class="bg-white p-3 rounded-lg border-2 border-[#1a4734] {{ $u->is_banned ? 'bg-red-50/70 border-[#870903]' : '' }}">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center space-x-1.5 mb-1">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase text-white {{ $u->role === 'admin' ? 'bg-[#870903]' : ($u->role === 'seller' ? 'bg-[#e90c00]' : 'bg-[#418b24]') }}">
                                {{ $u->role }}
                            </span>
                            @if($u->is_banned)
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-[#870903] text-white">
                                    BANNED
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-green-100 text-[#418b24]">
                                    Aktif
                                </span>
                            @endif
                        </div>
                        <h4 class="font-extrabold text-xs text-[#1a4734]">{{ $u->name }}</h4>
                        <p class="text-[11px] text-gray-600">{{ $u->email }}</p>
                        @if($u->phone)
                            <p class="text-[10px] text-gray-500">Telp: {{ $u->phone }}</p>
                        @endif
                    </div>

                    <div class="text-right text-[10px] text-gray-400">
                        ID: #{{ $u->id }}
                    </div>
                </div>

                <!-- Admin Action Buttons for Account Management -->
                @if($u->id !== Auth::id())
                    <div class="mt-2.5 pt-2 border-t border-gray-200 grid grid-cols-3 gap-1.5 text-center">
                        
                        <!-- 1. Ubah Role -->
                        <button 
                            type="button" 
                            @click="openRoleModal({{ json_encode($u) }})"
                            class="bg-[#1a4734] text-[#f9dd9c] py-1 px-1 rounded font-bold text-[10px] flex items-center justify-center space-x-0.5 hover:bg-[#143728]"
                        >
                            <span>Ubah Role</span>
                        </button>

                        <!-- 2. Ban / Unban -->
                        <form action="{{ route('admin.users.ban', $u->id) }}" method="POST">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full py-1 px-1 rounded font-black text-[10px] text-white uppercase {{ $u->is_banned ? 'bg-[#418b24]' : 'bg-[#e90c00]' }} hover:opacity-90"
                            >
                                {{ $u->is_banned ? 'Unban' : 'Ban Akun' }}
                            </button>
                        </form>

                        <!-- 3. Hapus Akun -->
                        <form action="{{ route('admin.users.delete', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }} secara permanen?');">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full bg-[#870903] text-white py-1 px-1 rounded font-black text-[10px] uppercase hover:opacity-90"
                            >
                                Hapus
                            </button>
                        </form>
                    </div>
                @else
                    <div class="mt-2 pt-1 border-t border-gray-200 text-center">
                        <span class="text-[10px] italic text-gray-400 font-semibold">(Akun Anda Sendiri)</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- SUB-TAB 2: PERINGATAN STOK KRITIS / MENIPIS -->
    <div x-show="currentTab === 'warnings'" class="space-y-2.5">
        <div class="flex items-center justify-between pb-1 border-b border-[#870903]">
            <h3 class="font-black text-xs text-[#870903] uppercase tracking-normal">Peringatan Barang Stok Kritis </h3>
            <span class="text-[10px] font-bold text-[#870903]">{{ $lowStockProducts->count() }} Barang Perlu Restock</span>
        </div>

        @forelse($lowStockProducts as $low)
            <div class="bg-white p-3 rounded-lg border-2 border-[#870903] flex items-center justify-between">
                <div>
                    <h4 class="font-extrabold text-xs text-[#1a4734]">{{ $low->name }}</h4>
                    <span class="text-[10px] text-gray-500 block">{{ $low->category }}</span>
                    <span class="font-black text-xs text-[#870903]">Rp {{ number_format($low->price, 0, ',', '.') }}</span>
                </div>

                <div class="text-right">
                    <span class="px-2.5 py-1 rounded text-xs font-black uppercase text-white {{ $low->stock <= 0 ? 'bg-[#870903]' : 'bg-[#e90c00]' }}">
                        {{ $low->stock <= 0 ? 'HABIS (0)' : 'Sisa ' . $low->stock }}
                    </span>
                    <a href="{{ route('seller.products') }}" class="block text-[10px] font-bold text-[#1a4734] hover:underline mt-1">
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white p-6 rounded-lg border-2 border-[#1a4734] text-center">
                <p class="font-bold text-xs text-[#418b24]">Semua stok barang aman dan tercukupi!</p>
            </div>
        @endforelse
    </div>

    <!-- UBAH ROLE MODAL -->
    <div 
        x-cloak 
        x-show="roleModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        @click.self="roleModal = false"
    >
        <div class="bg-[#f9dd9c] border-2 border-[#1a4734] w-full max-w-[360px] rounded p-4 text-[#1a4734]">
            <div class="flex items-center justify-between pb-2 border-b-2 border-[#1a4734] mb-3">
                <h3 class="font-black text-sm text-[#1a4734] uppercase tracking-wide">Ubah Hak Akses / Role</h3>
                <button type="button" @click="roleModal = false" class="text-xl font-black text-[#870903]">&times;</button>
            </div>

            <template x-if="selectedUser">
                <form :action="'{{ url('admin/users') }}/' + selectedUser.id + '/role'" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <p class="font-semibold text-gray-700">
                        Pengguna: <span class="font-bold text-[#1a4734]" x-text="selectedUser.name"></span>
                    </p>

                    <div>
                        <label class="block font-bold text-[#1a4734] mb-1">Pilih Role Baru</label>
                        <select name="role" x-model="selectedUser.role" class="w-full bg-white border border-[#1a4734] px-2.5 py-1.5 rounded font-bold text-[#1a4734] focus:outline-none">
                            <option value="user">User (Pelanggan Biasa)</option>
                            <option value="seller">Seller (Kasir POS & Toko)</option>
                            <option value="admin">Admin (Akses Penuh Keuangan & Sistem)</option>
                        </select>
                    </div>

                    <div class="pt-2 flex space-x-2">
                        <button type="submit" class="flex-1 bg-[#418b24] text-white py-2 rounded font-black text-xs uppercase hover:opacity-90">
                            Simpan Role
                        </button>
                        <button type="button" @click="roleModal = false" class="bg-gray-300 text-gray-800 px-3 py-2 rounded font-bold text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
