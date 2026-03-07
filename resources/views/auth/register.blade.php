@extends('layouts.app')

@section('content')
<style>
    .register-container {
        max-width: 400px;
        margin: 4rem auto;
        padding: 2.5rem;
    }
    
    .register-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .register-header h2 {
        margin: 0;
        font-size: 1.8rem;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .register-header p {
        color: var(--text-muted);
        margin-top: 0.5rem;
        font-size: 0.95rem;
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

    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.9rem;
    }

    .login-link a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .register-container {
            margin: 2rem 1rem;
            padding: 1.5rem;
        }
    }
</style>

<div class="register-container glass">
    <div class="register-header">
        <h2>Registrieren</h2>
        <p>Erstelle einen Account, um Ebooks unbegrenzt zu lesen.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">E-Mail</label>
            <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" required>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Passwort</label>
            <input type="password" id="password" class="form-control" name="password" required>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Passwort bestätigen</label>
            <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-submit">Registrieren</button>
    </form>
    
    <div class="login-link">
        Bereits registriert? <a href="{{ route('login') }}">Hier einloggen</a>
    </div>
</div>
@endsection
