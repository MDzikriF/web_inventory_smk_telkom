@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap');
    
    .custom-login-wrapper {
        font-family: 'Fredoka', 'Quicksand', sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }

    .main-title {
        color: #d32f2f;
        font-weight: 700;
        font-size: 2.2rem;
        margin-bottom: 20px;
        text-align: center;
    }

    .login-box {
        background: #f9f9f9;
        border: 2px solid #b5a999;
        border-radius: 15px;
        padding: 40px 40px;
        width: 100%;
    }

    .box-title {
        text-align: center;
        font-weight: 700;
        color: #7a6c6c;
        font-size: 1.3rem;
        margin-bottom: 5px;
    }

    .box-subtitle {
        text-align: center;
        color: #9c9292;
        font-size: 0.95rem;
        margin-bottom: 30px;
        line-height: 1.4;
    }

    .custom-form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .custom-label {
        display: block;
        font-weight: 700;
        color: #5d5454;
        margin-bottom: 8px;
        text-transform: uppercase;
        font-size: 1rem;
    }

    .custom-input {
        width: 100%;
        padding: 12px 45px 12px 15px;
        border: 1px solid #c4bfb7;
        border-radius: 8px;
        font-size: 1rem;
        font-family: inherit;
        background: #fdfdfd;
        color: #5d5454;
    }
    
    .custom-input:focus {
        outline: none;
        border-color: #d32f2f;
    }

    .custom-input::placeholder {
        color: #d1cdcd;
    }

    .icon-right {
        position: absolute;
        right: 15px;
        top: 38px;
        width: 24px;
        height: 24px;
        color: #000;
    }

    .custom-btn {
        width: 100%;
        background-color: #e52e2e;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 14px;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        margin-top: 10px;
        transition: background 0.2s;
        font-family: inherit;
    }

    .custom-btn:hover {
        background-color: #cc1f1f;
    }

    .bottom-text {
        text-align: center;
        margin-top: 15px;
        font-size: 0.9rem;
        color: #9c9292;
    }

    .bottom-text span {
        color: #000;
        font-weight: 600;
    }
</style>

<div class="custom-login-wrapper">
    <h1 class="main-title">Sistem Inventaris Lab</h1>
    
    <div class="login-box">
        <div class="box-title">MASUK KE AKUN ANDA</div>
        <p class="box-subtitle">Silakan masukkan detail akun anda untuk<br>mengakses sistem manajemen inventaris</p>

        @if($errors->any())
            <div style="color: #e52e2e; text-align:center; margin-bottom: 20px; font-weight: 500; font-size: 0.95rem;">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            
            <div class="custom-form-group">
                <label class="custom-label">NIP/NIS:</label>
                <input type="text" name="login" class="custom-input" placeholder="Masukkan NIP/NIS" value="{{ old('login') }}" required>
                <!-- User Icon -->
                <svg class="icon-right" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>

            <div class="custom-form-group">
                <label class="custom-label">KATA SANDI:</label>
                <input type="password" name="password" class="custom-input" placeholder="Masukkan kata sandi" required>
                <!-- Hidden Password Icon -->
                <svg class="icon-right" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>
                </svg>
            </div>

            <button type="submit" class="custom-btn">MASUK</button>

            <div class="bottom-text">
                Belum punya akun? <span>Hubungi admin</span>
            </div>
        </form>
    </div>
</div>
@endsection
