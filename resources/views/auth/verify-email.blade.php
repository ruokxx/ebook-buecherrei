@extends('layouts.app')

@section('content')
<div class="glass" style="max-width: 600px; margin: 4rem auto; padding: 3rem; text-align: center; border-radius: 12px; border: 1px solid var(--glass-border); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
    <h2 style="margin-top: 0; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">E-Mail Bestätigung erforderlich</h2>
    
    <p style="color: var(--text-light); font-size: 1.1rem; line-height: 1.6; margin: 2rem 0;">
        Vielen Dank für deine Registrierung! Bevor du unbegrenzt Bücher lesen kannst, musst du deine E-Mail-Adresse bestätigen. 
        <br><br>
        Wir haben dir soeben einen Link zugeschickt. Bitte überprüfe dein Postfach (und deinen Spam-Ordner).
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn" style="padding: 0.8rem 1.5rem; font-size: 1.05rem;">
            E-Mail erneut senden
        </button>
    </form>
</div>
@endsection
