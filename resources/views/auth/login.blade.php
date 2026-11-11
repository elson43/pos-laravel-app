@extends('layouts.auth')

@section('title', 'Masuk ke Sistem - RetailSmart')

@section('content')
<form action="{{ route('login.post') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">Alamat Email</label>
        <input 
            type="email" 
            name="email" 
            id="email" 
            class="form-control @error('email') is-invalid @enderror" 
            placeholder="nama@pos.com" 
            value="{{ old('email', 'kasir@pos.com') }}" 
            required 
            autofocus
        >
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
            <label for="password" class="form-label" style="margin-bottom: 0;">Kata Sandi</label>
        </div>
        <input 
            type="password" 
            name="password" 
            id="password" 
            class="form-control @error('password') is-invalid @enderror" 
            placeholder="••••••••" 
            value="password123" 
            required
        >
        @error('password')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.85rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--slate-300);">
            <input type="checkbox" name="remember" style="accent-color: var(--emerald-500);">
            <span>Ingat saya</span>
        </label>
    </div>

    <button type="submit" class="btn btn-emerald" style="width: 100%; padding: 0.75rem; font-size: 0.95rem;">
        Masuk ke Sistem
    </button>
</form>

<div class="demo-credentials-box">
    <div style="font-weight: 700; color: var(--slate-300); margin-bottom: 0.4rem;">
        🔑 Akses Cepat Akun Demo (Klik untuk isi otomatis):
    </div>
    <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-bottom: 0.5rem;">
        <span class="demo-tag demo-kasir" onclick="fillCreds('kasir@pos.com', 'password123')">
            UKasir (kasir@pos.com)
        </span>
        <span class="demo-tag demo-gudang" onclick="fillCreds('gudang@pos.com', 'password123')">
            UGudang (gudang@pos.com)
        </span>
        <span class="demo-tag demo-admin" onclick="fillCreds('admin@pos.com', 'password123')">
            UAdmin (admin@pos.com)
        </span>
    </div>
    <div style="color: var(--slate-400); font-size: 0.75rem;">
        Kata sandi standar: <code>password123</code>. Sistem otomatis mengarahkan ke antarmuka peran masing-masing.
    </div>
</div>

<div style="text-align: center; margin-top: 1.75rem; font-size: 0.875rem; color: var(--slate-400);">
    Belum punya akun? 
    <a href="{{ route('register') }}" style="color: var(--emerald-400); font-weight: 600; text-decoration: underline;">
        Daftar Akun Baru
    </a>
</div>
@endsection

@section('scripts')
<script>
    function fillCreds(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
