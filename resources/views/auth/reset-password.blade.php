@extends('layouts.mobile', ['title' => 'Buat Password Baru - Toko Lily'])

@section('content')
<div class="pt-4 pb-6">

    <div class="bg-[#1a4734] text-white p-5 rounded-lg text-center mb-5 border-2 border-[#1a4734]">
        <h1 class="text-xl font-black text-[#f9dd9c] tracking-wide">BUAT KATA SANDI BARU</h1>
        <p class="text-xs text-gray-200 mt-1">Perbarui kata sandi akun Anda</p>
    </div>

    <div class="bg-white p-5 rounded-lg border-2 border-[#1a4734]">
        <form action="{{ route('password.update') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#1a4734] mb-1">Email Akun</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email', $email) }}" 
                    required 
                    readonly
                    class="w-full bg-gray-100 border-2 border-gray-300 px-3 py-2 rounded text-xs font-semibold text-gray-700"
                >
            </div>

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-xs font-bold text-[#1a4734] mb-1">Kata Sandi Baru</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="Minimal 6 karakter" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-3 py-2 rounded text-xs font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-[#1a4734] mb-1">Konfirmasi Kata Sandi Baru</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    placeholder="Ulangi kata sandi baru" 
                    class="w-full bg-[#f9dd9c]/30 border-2 border-[#1a4734] px-3 py-2 rounded text-xs font-semibold text-[#1a4734] focus:outline-none focus:border-[#418b24]"
                >
            </div>

            <button type="submit" class="w-full bg-[#418b24] text-white py-2.5 rounded font-black text-sm uppercase tracking-wider hover:opacity-90 active:scale-[0.99] transition-transform">
                Simpan Password & Login
            </button>
        </form>
    </div>

</div>
@endsection
