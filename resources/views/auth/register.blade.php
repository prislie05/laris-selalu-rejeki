@extends('layouts.mobile', ['title' => 'Daftar Akun - Laris Selalu Rejeki', 'pageBg' => '#f9dd9c'])

@section('content')
<div class="my-auto py-6 w-full space-y-5">

    <!-- Store Brand Header -->
    <div class="bg-[#1a4734] text-white p-5 rounded-xl text-center border-2 border-[#1a4734]">
        <div class="w-16 h-16 bg-[#f9dd9c] text-[#1a4734] rounded-full mx-auto flex items-center justify-center font-black text-3xl mb-2.5 border-2 border-white">
            LSR
        </div>
        <h1 class="text-2xl font-black text-[#f9dd9c] tracking-wide">Hi, Selamat Datang</h1>
        <p class="text-sm font-semibold text-gray-200 mt-0.5">Buat akun untuk mulai berbelanja di Laris Selalu Rejeki</p>
    </div>

    <!-- Registration Form Card -->
    <div class="bg-white p-6 rounded-xl border-2 border-[#1a4734]">
        <h2 class="text-xl font-extrabold text-[#1a4734] mb-6 pb-3.5 border-b-2 border-[#1a4734] flex items-center space-x-2.5">
            <svg class="w-6 h-6 text-[#418b24]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <span>Formulir Registrasi Akun</span>
        </h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label class="block text-base font-bold text-[#1a4734] mb-2">Nama Lengkap</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    placeholder="Contoh: Lily Rahmawati" 
                    style="min-height: 54px; border-radius: 8px;"
                    class="w-full min-h-[54px] bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3.5 rounded-[8px] text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div class="mb-5">
                <label class="block text-base font-bold text-[#1a4734] mb-2">Alamat Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="nama@email.com" 
                    style="min-height: 54px; border-radius: 8px;"
                    class="w-full min-h-[54px] bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3.5 rounded-[8px] text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div class="mb-5">
                <label class="block text-base font-bold text-[#1a4734] mb-2">Nomor Telepon (WhatsApp)</label>
                <input 
                    type="text" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    placeholder="0812xxxxxxxx" 
                    style="min-height: 54px; border-radius: 8px;"
                    class="w-full min-h-[54px] bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3.5 rounded-[8px] text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div class="mb-5">
                <label class="block text-base font-bold text-[#1a4734] mb-2">Kata Sandi (Minimal 6 Karakter)</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••" 
                    style="min-height: 54px; border-radius: 8px;"
                    class="w-full min-h-[54px] bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3.5 rounded-[8px] text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div class="mb-6">
                <label class="block text-base font-bold text-[#1a4734] mb-2">Konfirmasi Kata Sandi</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    placeholder="••••••••" 
                    style="min-height: 54px; border-radius: 8px;"
                    class="w-full min-h-[54px] bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-4 py-3.5 rounded-[8px] text-lg font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <!-- Tombol Daftar & Masuk Otomatis: diperlebar 3px ke bawah & teks center di tengah -->
            <div class="mb-5">
                <button 
                    type="submit" 
                    style="min-height: 55px; padding-top: 14px; padding-bottom: 17px; border-radius: 8px;"
                    class="w-full min-h-[55px] pt-3.5 pb-[17px] px-6 bg-[#418b24] text-white rounded-[8px] font-black text-lg uppercase tracking-wider hover:opacity-95 active:scale-[0.99] transition-transform flex items-center justify-center text-center leading-normal shadow-sm"
                >
                    Daftar & Masuk Otomatis
                </button>
            </div>

            <!-- Pembatas ATAU -->
            <div class="relative flex items-center justify-center my-5">
                <div class="border-t-2 border-gray-200 w-full"></div>
                <span class="bg-white px-3 text-xs font-black text-gray-500 uppercase tracking-widest absolute">ATAU</span>
            </div>

            <!-- Tombol Call To Action: Daftar/Masuk dengan Akun Google -->
            <div class="mb-6">
                <a 
                    href="{{ route('auth.google') }}" 
                    style="min-height: 55px; border-radius: 8px;"
                    class="w-full min-h-[55px] px-5 bg-white hover:bg-gray-50 active:scale-[0.99] border-2 border-[#1a4734] text-[#1a4734] rounded-[8px] font-black text-lg transition-transform flex items-center justify-center text-center space-x-3 shadow-sm"
                >
                    <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Daftar Cepat dengan Google</span>
                </a>
            </div>
        </form>

        <div class="mt-6 pt-5 border-t-2 border-gray-100 text-center">
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
