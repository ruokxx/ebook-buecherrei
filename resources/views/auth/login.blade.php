@extends('layouts.app')

@section('content')
<style>
    .login-container {
        max-width: 400px;
        margin: 4rem auto;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 2.5rem;
        backdrop-filter: blur(12px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
    }
    
    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .login-header h1 {
        margin: 0;
        font-size: 1.8rem;
        color: #fff;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-light);
        font-weight: 600;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        border-radius: 8px;
        font-family: inherit;
        font-size: 1rem;
        box-sizing: border-box;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
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
</style>

<div class="login-container">
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
