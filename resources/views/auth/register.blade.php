@extends('layouts.auth')

@section('title', 'Pendaftaran Akun Baru - RetailSmart')

@section('content')
<form action="{{ route('register.post') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="name" class="form-label">Nama Lengkap</label>
        <input 
            type="text" 
            name="name" 
            id="name" 
            class="form-control @error('name') is-invalid @enderror" 
            placeholder="Contoh: Rian Pratama" 
            value="{{ old('name') }}" 
            required 
            autofocus
        >
        @error('name')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="email" class="form-label">Alamat Email</label>
        <input 
            type="email" 
            name="email" 
            id="email" 
            class="form-control @error('email') is-invalid @enderror" 
            placeholder="nama@email.com" 
            value="{{ old('email') }}" 
            required
        >
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Pilih Peran Akun (Role Akses) <span style="color: var(--emerald-400);">*</span></label>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-top: 0.35rem;">
            <label style="cursor: pointer;">
                <input type="radio" name="role" value="UKasir" {{ old('role', 'UKasir') === 'UKasir' ? 'checked' : '' }} style="display: none;" class="role-radio">
                <div class="role-box" data-role="UKasir">
                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--emerald-400);">UKasir</div>
                    <div style="font-size: 0.7rem; color: var(--slate-400); margin-top: 2px;">POS & Kasir</div>
                </div>
            </label>
            <label style="cursor: pointer;">
                <input type="radio" name="role" value="UGudang" {{ old('role') === 'UGudang' ? 'checked' : '' }} style="display: none;" class="role-radio">
                <div class="role-box" data-role="UGudang">
                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--amber-400);">UGudang</div>
                    <div style="font-size: 0.7rem; color: var(--slate-400); margin-top: 2px;">Inventaris</div>
                </div>
            </label>
            <label style="cursor: pointer;">
                <input type="radio" name="role" value="UAdmin" {{ old('role') === 'UAdmin' ? 'checked' : '' }} style="display: none;" class="role-radio">
                <div class="role-box" data-role="UAdmin">
                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--white);">UAdmin</div>
                    <div style="font-size: 0.7rem; color: var(--slate-400); margin-top: 2px;">Monitoring</div>
                </div>
            </label>
        </div>
        @error('role')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Kata Sandi</label>
        <input 
            type="password" 
            name="password" 
            id="password" 
            class="form-control @error('password') is-invalid @enderror" 
            placeholder="Minimal 6 karakter" 
            required
        >
        @error('password')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
        <input 
            type="password" 
            name="password_confirmation" 
            id="password_confirmation" 
            class="form-control" 
            placeholder="Ulangi kata sandi" 
            required
        >
    </div>

    <button type="submit" class="btn btn-emerald" style="width: 100%; padding: 0.75rem; font-size: 0.95rem; margin-top: 1rem;">
        Daftar & Masuk
    </button>
</form>

<div style="text-align: center; margin-top: 1.75rem; font-size: 0.875rem; color: var(--slate-400);">
    Sudah memiliki akun? 
    <a href="{{ route('login') }}" style="color: var(--emerald-400); font-weight: 600; text-decoration: underline;">
        Masuk di Sini
    </a>
</div>
@endsection

@section('scripts')
<style>
    .role-box {
        background-color: var(--slate-850);
        border: 2px solid var(--slate-700);
        border-radius: var(--radius-md);
        padding: 0.65rem 0.5rem;
        text-align: center;
        transition: all 0.15s ease;
    }
    .role-radio:checked + .role-box {
        border-color: var(--emerald-500);
        background-color: var(--slate-800);
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
</style>
@endsection
