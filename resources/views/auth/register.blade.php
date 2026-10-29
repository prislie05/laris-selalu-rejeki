@extends('layouts.mobile', ['title' => 'Daftar Akun - Toko Lily'])

@section('content')
<div class="pt-4 pb-6">

    <!-- Store Brand Header -->
    <div class="bg-[#1a4734] text-white p-5 rounded-lg text-center mb-5 border-2 border-[#1a4734]">
        <div class="w-14 h-14 bg-[#f9dd9c] text-[#1a4734] rounded-full mx-auto flex items-center justify-center font-black text-2xl mb-2 border-2 border-white">
            TL
        </div>
        <h1 class="text-xl font-black text-[#f9dd9c] tracking-wide">DAFTAR PELANGGAN</h1>
        <p class="text-xs text-gray-200 mt-1">Buat akun untuk mulai berbelanja di Toko Lily</p>
    </div>

    <!-- Registration Form Card -->
    <div class="bg-white p-5 rounded-lg border-2 border-[#1a4734]">
        <h2 class="text-base font-extrabold text-[#1a4734] mb-4 pb-2 border-b-2 border-[#1a4734] flex items-center space-x-2">
            <svg class="w-5 h-5 text-[#418b24]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <span>Formulir Registrasi Akun</span>
        </h2>

        <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#1a4734] mb-1">Nama Lengkap</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    placeholder="Contoh: Lily Rahmawati" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-3 py-2 rounded text-xs font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-[#1a4734] mb-1">Alamat Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="nama@email.com" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-3 py-2 rounded text-xs font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-[#1a4734] mb-1">Nomor Telepon (WhatsApp)</label>
                <input 
                    type="text" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    placeholder="0812xxxxxxxx" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-3 py-2 rounded text-xs font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-[#1a4734] mb-1">Kata Sandi (Minimal 6 Karakter)</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-3 py-2 rounded text-xs font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-[#1a4734] mb-1">Konfirmasi Kata Sandi</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    placeholder="••••••••" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-3 py-2 rounded text-xs font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-[#418b24] text-white py-2.5 rounded font-black text-sm uppercase tracking-wider hover:opacity-90 active:scale-[0.99] transition-transform">
                    Daftar & Masuk Otomatis
                </button>
            </div>
        </form>

        <div class="mt-4 pt-3 border-t border-gray-200 text-center">
            <p class="text-xs text-[#1a4734]">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-extrabold text-[#418b24] hover:underline ml-1">
                    Login di Sini
                </a>
            </p>
        </div>
    </div>

</div>
@endsection
