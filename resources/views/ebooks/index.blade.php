@extends('layouts.app')

@section('content')
<style>
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    h1 {
        margin: 0;
        font-weight: 700;
    }

    .netflix-row {
        display: flex;
        overflow-x: auto;
        gap: 1.5rem;
        padding-bottom: 2rem;
        padding-top: 1rem;
        /* Hide scrollbar for a cleaner look */
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.2) transparent;
    }
    
    .netflix-row::-webkit-scrollbar {
        height: 8px;
    }
    .netflix-row::-webkit-scrollbar-track {
        background: transparent;
    }
    .netflix-row::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.2);
        border-radius: 4px;
    }

    .book-card {
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease, background 0.4s ease;
        backdrop-filter: blur(10px);
        min-width: 200px;
        flex-shrink: 0;
        position: relative;
    }

    .book-card:hover {
        transform: scale(1.08) translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.8);
        background: rgba(30, 41, 59, 0.95);
        z-index: 10;
        border-color: rgba(255,255,255,0.2);
    }

    .book-cover {
        width: 130px;
        height: 180px;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        border-radius: 4px 12px 12px 4px;
        box-shadow: inset 4px 0 10px rgba(0,0,0,0.5), 5px 5px 15px rgba(0,0,0,0.5);
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        position: relative;
        font-weight: 800;
        letter-spacing: 2px;
        color: rgba(255,255,255,0.8);
        border-right: 1px solid rgba(255,255,255,0.05);
    }

    .book-cover::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: rgba(0,0,0,0.3);
        box-shadow: 1px 0 2px rgba(255,255,255,0.1);
    }
    
    .book-cover.txt { background: linear-gradient(135deg, #065f46, #047857); }
    .book-cover.pdf { background: linear-gradient(135deg, #991b1b, #b91c1c); }

    .book-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #fff;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }

    .book-meta {
        font-size: 0.8rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .book-actions {
        margin-top: auto;
        display: flex;
        gap: 0.5rem;
        width: 100%;
        opacity: 0.8;
        transition: opacity 0.3s;
    }

    .book-card:hover .book-actions {
        opacity: 1;
    }

    .book-actions a {
        flex: 1;
        text-align: center;
        padding: 0.6rem;
        font-size: 0.9rem;
        border-radius: 8px;
    }

    .empty-state {
        text-align: center;
        padding: 4rem;
        background: var(--glass-bg);
        border: 1px dashed var(--glass-border);
        border-radius: 12px;
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .empty-state h3 {
        margin-bottom: 0.5rem;
    }
    
    h2.row-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #f8fafc;
        opacity: 0.9;
    }
</style>

<div class="header">
    <h1>Meine Bibliothek</h1>
</div>

@if($ebooks->count() > 0)
    <h2 class="row-title">Zuletzt hinzugefügt</h2>
@endif

<div class="netflix-row">
    @forelse($ebooks as $book)
        <div class="book-card">
            <div class="book-cover {{ strtolower($book->file_type) }}">
                {{ strtoupper($book->file_type) }}
            </div>
            <div class="book-title" title="{{ $book->title }}">
                {{ Str::limit($book->title, 35) }}
            </div>
            <div class="book-meta">
                {{ $book->chapters_count }} Kapitel &bull; {{ strtoupper($book->file_type) }}
            </div>
            <div class="book-actions">
                <a href="{{ route('ebooks.read', $book) }}" class="btn" style="width: 100%;">JETZT LESEN</a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <h3>Noch keine Bücher vorhanden</h3>
            <p class="book-meta" style="color:var(--text-muted)">Lade dein erstes Ebook hoch, um zu starten.</p>
            <br>
            <a href="{{ route('ebooks.create') }}" class="btn">Jetzt hochladen</a>
        </div>
    @endforelse
</div>

@endsection
