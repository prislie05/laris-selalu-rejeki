@extends('layouts.mobile', ['title' => 'Daftar Akun - Toko Lily'])

@section('content')
<div class="pt-4 pb-8 space-y-5">

    <!-- Store Brand Header -->
    <div class="bg-[#1a4734] text-white p-6 rounded-xl text-center border-2 border-[#1a4734]">
        <div class="w-16 h-16 bg-[#f9dd9c] text-[#1a4734] rounded-full mx-auto flex items-center justify-center font-black text-3xl mb-3 border-2 border-white">
            TL
        </div>
        <h1 class="text-2xl font-black text-[#f9dd9c] tracking-wide">DAFTAR PELANGGAN</h1>
        <p class="text-sm font-semibold text-gray-200 mt-1">Buat akun untuk mulai berbelanja di Toko Lily</p>
    </div>

    <!-- Registration Form Card -->
    <div class="bg-white p-6 rounded-xl border-2 border-[#1a4734]">
        <h2 class="text-xl font-extrabold text-[#1a4734] mb-5 pb-3 border-b-2 border-[#1a4734] flex items-center space-x-2">
            <svg class="w-6 h-6 text-[#418b24]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <span>Formulir Registrasi Akun</span>
        </h2>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Nama Lengkap</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    placeholder="Contoh: Lily Rahmawati" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-lg text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Alamat Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="nama@email.com" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-lg text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Nomor Telepon (WhatsApp)</label>
                <input 
                    type="text" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    placeholder="0812xxxxxxxx" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-lg text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Kata Sandi (Minimal 6 Karakter)</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-lg text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-base font-bold text-[#1a4734] mb-1.5">Konfirmasi Kata Sandi</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    placeholder="••••••••" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3 rounded-lg text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full min-h-[52px] bg-[#418b24] text-white py-3.5 px-4 rounded-lg font-black text-lg uppercase tracking-wider hover:opacity-95 active:scale-[0.99] transition-transform">
                    Daftar & Masuk Otomatis
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t-2 border-gray-100 text-center">
            <p class="text-base text-[#1a4734]">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-extrabold text-[#418b24] hover:underline ml-1">
                    Login di Sini
                </a>
            </p>
        </div>
    </div>

</div>
@endsection
