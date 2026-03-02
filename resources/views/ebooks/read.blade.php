@extends('layouts.app')

@section('content')
<style>
    .reader-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        background: var(--glass-bg);
        padding: 1rem 2rem;
        border-radius: 12px;
        border: 1px solid var(--glass-border);
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .reader-header h1 {
        margin: 0;
        font-size: 1.5rem;
        flex: 1;
        min-width: 250px;
    }

    .btn-back {
        background: rgba(255,255,255,0.1);
        color: var(--text-light);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .btn-back:hover {
        background: rgba(255,255,255,0.2);
    }

    .reader-container {
        width: 100%;
        min-height: 75vh;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        overflow: hidden;
        backdrop-filter: blur(12px);
    }

    .pdf-frame {
        width: 100%;
        height: 80vh;
        border: none;
        display: block;
    }

    .txt-reader {
        padding: 2rem;
        font-family: 'Georgia', serif; 
        font-size: 1.15rem;
        line-height: 1.8;
        color: #e2e8f0;
        max-width: 800px;
        margin: 0 auto;
        white-space: pre-wrap;
    }

    .pagination-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background: rgba(0,0,0,0.2);
        border-top: 1px solid var(--glass-border);
        border-bottom: 1px solid var(--glass-border);
    }

    .pagination-controls .btn-page {
        background: var(--primary);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.2s;
    }

    .pagination-controls .btn-page.disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    @media (max-width: 768px) {
        .reader-header {
            padding: 1rem;
            flex-direction: column;
            align-items: flex-start;
        }
        .txt-reader {
            padding: 1rem;
            font-size: 1rem;
        }
        .pagination-controls {
            padding: 1rem;
        }
    }
</style>

<div class="reader-header">
    <a href="{{ route('ebooks.index') }}" class="btn-back">
        &larr; Zurück
    </a>
    <h1>{{ $ebook->title }}</h1>
    <div>
        <span style="opacity: 0.7; font-size: 0.9rem;">
            Format: {{ strtoupper($ebook->file_type) }} | {{ $ebook->chapters_count }} Kapitel
        </span>
    </div>
</div>

<div class="reader-container">
    @if(strtolower($ebook->file_type) === 'pdf')
        <iframe class="pdf-frame" src="{{ route('ebooks.stream', $ebook) }}#toolbar=0" title="PDF Reader"></iframe>
    @elseif(strtolower($ebook->file_type) === 'txt')
        
        @if($totalPages > 1)
        <div class="pagination-controls">
            <a href="?page={{ $currentPage - 1 }}" class="btn-page {{ $currentPage <= 1 ? 'disabled' : '' }}">&larr; Vorherige Seite</a>
            <span>Seite {{ $currentPage }} von {{ $totalPages }}</span>
            <a href="?page={{ $currentPage + 1 }}" class="btn-page {{ $currentPage >= $totalPages ? 'disabled' : '' }}">Nächste Seite &rarr;</a>
        </div>
        @endif
        
        <div class="txt-reader">{!! $textContent !!}</div>
        
        @if($totalPages > 1)
        <div class="pagination-controls" style="border-top: 1px solid var(--glass-border); border-bottom: none;">
            <a href="?page={{ $currentPage - 1 }}" class="btn-page {{ $currentPage <= 1 ? 'disabled' : '' }}">&larr; Vorherige Seite</a>
            <span>Seite {{ $currentPage }} von {{ $totalPages }}</span>
            <a href="?page={{ $currentPage + 1 }}" class="btn-page {{ $currentPage >= $totalPages ? 'disabled' : '' }}">Nächste Seite &rarr;</a>
        </div>
        @endif
        
    @else
        <div style="padding: 2rem; text-align: center;">Format nicht unterstützt.</div>
    @endif
</div>

@endsection
