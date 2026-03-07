@extends('layouts.app')

@section('content')
<style>
    .login-container {
        max-width: 400px;
        margin: 4rem auto;
        padding: 2.5rem;
        /* Using global .glass class for background, border, blur, shadow */
    }
    
    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .login-header h1 {
        margin: 0;
        font-size: 1.8rem;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .btn-submit {
        width: 100%;
        margin-top: 1rem;
        padding: 0.8rem;
        font-size: 1.1rem;
    }
    
    .text-danger {
        color: #f87171;
        font-size: 0.85rem;
        margin-top: 0.25rem;
        display: block;
    }

    @media (max-width: 768px) {
        .login-container {
            margin: 2rem 1rem;
            padding: 1.5rem;
        }
    }
</style>

<div class="login-container glass">
    <div class="login-header">
        <h1>Admin Login</h1>
    </div>
    
    <form method="POST" action="/login">
        @csrf
        
        <div class="form-group">
            <label for="email">E-Mail Adresse</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Passwort</label>
            <input id="password" type="password" class="form-control" name="password" required>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-submit">
            Einloggen
        </button>
    </form>
</div>
@endsection
