@extends('layouts.app')

@section('content')
<style>
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap; 
        gap: 1rem;
    }
    
    .admin-header h1 {
        margin: 0;
        font-weight: 700;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    @media(max-width: 900px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }

    .settings-panel {
        padding: 2rem;
        border-radius: 12px;
    }
    
    .settings-panel h3 {
        margin-top: 0;
        margin-bottom: 1.5rem;
        color: var(--primary);
        font-size: 1.25rem;
        border-bottom: 1px solid var(--glass-border);
        padding-bottom: 0.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .info-text {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 0.5rem;
        line-height: 1.5;
    }
    
    .btn-submit {
        margin-top: 1rem;
        width: 100%;
        padding: 1rem;
        font-size: 1.1rem;
    }
</style>

<div class="admin-header">
    <h1>Admin Panel - Einstellungen</h1>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('admin.index') }}" class="btn btn-outline">Bücher</a>
        <a href="{{ route('admin.users') }}" class="btn btn-outline">Nutzer</a>
        <a href="{{ route('admin.settings') }}" class="btn">Einstellungen</a>
        <a href="{{ route('ebooks.create') }}" class="btn btn-outline">+ Hochladen</a>
    </div>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <div class="settings-grid">
        <!-- SMTP Settings -->
        <div class="settings-panel glass">
            <h3>E-Mail Server (SMTP)</h3>
            
            <div class="form-group">
                <label>SMTP Host</label>
                <input type="text" class="form-control" name="smtp_host" value="{{ $settings['smtp_host'] }}">
            </div>

            <div class="form-group">
                <label>SMTP Port</label>
                <input type="text" class="form-control" name="smtp_port" value="{{ $settings['smtp_port'] }}">
            </div>

            <div class="form-group">
                <label>Verschlüsselung (z.B. tls, ssl)</label>
                <input type="text" class="form-control" name="smtp_encryption" value="{{ $settings['smtp_encryption'] }}">
            </div>

            <div class="form-group">
                <label>SMTP Benutzer</label>
                <input type="text" class="form-control" name="smtp_user" value="{{ $settings['smtp_user'] }}">
            </div>

            <div class="form-group">
                <label>SMTP Passwort</label>
                <input type="password" class="form-control" name="smtp_password" value="{{ $settings['smtp_password'] }}" placeholder="Wird geladen/versteckt">
            </div>

            <div class="form-group">
                <label>Absender Adresse (From)</label>
                <input type="text" class="form-control" name="mail_from_address" value="{{ $settings['mail_from_address'] }}">
            </div>

            <div class="form-group">
                <label>Absender Name (From Name)</label>
                <input type="text" class="form-control" name="mail_from_name" value="{{ $settings['mail_from_name'] }}">
            </div>
        </div>

        <!-- Email Content -->
        <div class="settings-panel glass">
            <h3>E-Mail Bestätigung Text</h3>
            
            <div class="info-text" style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <strong>Verfügbare Platzhalter:</strong><br>
                <code>{name}</code> - Name des neu registrierten Nutzers<br>
                <code>{verification_url}</code> - Der Bestätigungslink als Text<br>
                <code>{verification_button}</code> - Generiert an dieser Zeile den klickbaren Laravel Bestätigungsbutton
            </div>

            <div class="form-group">
                <label>E-Mail Betreff (Subject)</label>
                <input type="text" class="form-control" name="verification_email_subject" value="{{ $settings['verification_email_subject'] }}">
            </div>

            <div class="form-group">
                <label>E-Mail Inhalt (Body)</label>
                <textarea class="form-control" name="verification_email_body" rows="12">{{ $settings['verification_email_body'] }}</textarea>
            </div>
            
        </div>

        <!-- AI Settings -->
        <div class="settings-panel glass" style="grid-column: 1 / -1;">
            <h3>KI-Buchgenerator (OpenAI)</h3>
            
            <div class="info-text" style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                Damit Nutzer interaktiv Bücher generieren lassen können, wird ein API-Key benötigt (z. B. von OpenAI für ChatGPT).
            </div>

            <div class="form-group">
                <label>LLM Provider API Key</label>
                <input type="password" class="form-control" name="llm_api_key" value="{{ $settings['llm_api_key'] ?? '' }}" placeholder="API Key hier eintragen">
            </div>
        </div>
    </div>
    
    <button type="submit" class="btn btn-submit">Einstellungen Speichern</button>
</form>

@endsection
