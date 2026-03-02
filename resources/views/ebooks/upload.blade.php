@extends('layouts.app')

@section('content')
<style>
    .upload-container {
        max-width: 600px;
        margin: 2rem auto;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 2.5rem;
        backdrop-filter: blur(12px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }

    .upload-container h2 {
        margin-top: 0;
        margin-bottom: 2rem;
        text-align: center;
        font-weight: 700;
        font-size: 1.8rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-light);
    }

    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px dashed var(--glass-border);
        border-radius: 12px;
        padding: 3rem 1rem;
        cursor: pointer;
        transition: border-color 0.3s, background 0.3s;
        text-align: center;
        background: rgba(255,255,255,0.02);
    }

    .file-input-wrapper:hover {
        border-color: var(--primary);
        background: rgba(59, 130, 246, 0.05);
    }

    .file-input-wrapper input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
        height: 100%;
        width: 100%;
    }

    .file-input-text {
        pointer-events: none;
    }

    .file-input-text svg {
        width: 48px;
        height: 48px;
        margin-bottom: 1rem;
        color: var(--primary);
    }
    
    .file-input-text p {
        margin: 0;
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .file-input-text .highlight {
        color: var(--primary);
        font-weight: 600;
    }

    .btn-submit {
        width: 100%;
        padding: 1rem;
        font-size: 1.1rem;
        border-radius: 12px;
        margin-top: 1rem;
    }

    .file-name-display {
        margin-top: 1rem;
        padding: 0.8rem;
        background: rgba(255,255,255,0.05);
        border-radius: 8px;
        font-size: 0.9rem;
        display: none;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<div class="upload-container">
    <h2>Ebook Hochladen</h2>
    
    <form action="{{ route('ebooks.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label>Wähle dein Ebook (TXT oder PDF)</label>
            <div class="file-input-wrapper" id="drop-zone">
                <input type="file" name="ebook" id="ebook-input" accept=".txt,.pdf" required>
                <div class="file-input-text">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p><span class="highlight">Klicke</span> oder ziehe eine Datei hierhin</p>
                    <p style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.7;">Unterstützte Formate: .txt, .pdf</p>
                </div>
            </div>
            <div class="file-name-display" id="file-name-display">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span id="file-name-text">Keine Datei ausgewählt</span>
            </div>
            @error('ebook')
                <div class="alert alert-error" style="margin-top: 1rem; margin-bottom: 0; padding: 0.5rem;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-submit">Hochladen & Verarbeiten</button>
    </form>
</div>

<script>
    const input = document.getElementById('ebook-input');
    const display = document.getElementById('file-name-display');
    const text = document.getElementById('file-name-text');

    input.addEventListener('change', function(e) {
        if(this.files && this.files.length > 0) {
            text.textContent = this.files[0].name;
            display.style.display = 'flex';
        } else {
            display.style.display = 'none';
        }
    });
</script>
@endsection
