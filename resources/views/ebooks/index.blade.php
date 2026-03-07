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
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .netflix-row {
        display: flex;
        overflow-x: auto;
        gap: 1.5rem;
        padding-bottom: 2.5rem;
        padding-top: 1.5rem;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.15) transparent;
        scroll-snap-type: x mandatory;
        padding-left: 0.5rem; /* Better edge padding on mobile */
        padding-right: 0.5rem;
    }
    
    .netflix-row::-webkit-scrollbar {
        height: 8px;
    }
    .netflix-row::-webkit-scrollbar-track {
        background: transparent;
    }
    .netflix-row::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.15);
        border-radius: 4px;
    }

    .book-card {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease, border-color 0.4s ease;
        min-width: 200px;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
        scroll-snap-align: start;
        /* Using global glass class covers background/border/blur */
    }

    /* For non-hover devices (mobile), subtly show actions, or just rely on tap to hover */
    @media (hover: hover) {
        .book-card:hover {
            transform: scale(1.05) translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 15px rgba(249, 115, 22, 0.2);
            z-index: 10;
            border-color: rgba(249, 115, 22, 0.6);
        }
        .book-card:hover .book-actions {
            opacity: 1;
            transform: translateY(0);
        }
        .book-actions {
            opacity: 0;
            transform: translateY(10px);
        }
    }

    .book-cover {
        width: 140px;
        height: 195px;
        border-radius: 4px 12px 12px 4px;
        box-shadow: inset 4px 0 10px rgba(0,0,0,0.6), 5px 5px 15px rgba(0,0,0,0.5);
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        position: relative;
        font-weight: 800;
        letter-spacing: 2px;
        color: rgba(255,255,255,0.95);
        border-right: 1px solid rgba(255,255,255,0.1);
        background-color: #0f172a;
        background-size: cover;
        background-position: center;
        overflow: hidden;
    }

    .book-cover::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.6), rgba(0, 0, 0, 0.9));
        z-index: 1;
    }

    .book-cover > span {
        z-index: 2;
        text-shadow: 0 2px 10px rgba(0,0,0,0.9);
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: normal;
        color: rgba(255,255,255,0.95);
        text-align: center;
        padding: 0 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }

    .book-cover::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: rgba(0,0,0,0.5);
        box-shadow: 1px 0 2px rgba(255,255,255,0.2);
        z-index: 3;
    }

    .book-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--text-light);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .book-meta {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 1.2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .book-actions {
        margin-top: auto;
        display: flex;
        width: 100%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .book-actions a {
        flex: 1;
        text-align: center;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    h2.row-title {
        font-size: 1.35rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        margin-top: 1.5rem;
        color: var(--text-light);
        border-left: 4px solid var(--primary);
        padding-left: 1rem;
    }

    @media (max-width: 768px) {
        .book-card {
            min-width: 160px;
            padding: 0.75rem;
        }
        .book-cover {
            width: 110px;
            height: 160px;
        }
        .book-actions {
            opacity: 1;
            transform: none;
        }
        .book-title {
            font-size: 1rem;
        }
        h2.row-title {
            font-size: 1.2rem;
        }
    }
</style>

<div class="header">
    <h1>Die Bücherei</h1>
</div>

@forelse($genres as $genreName => $ebooks)
    <h2 class="row-title">{{ $genreName }}</h2>
    <div class="netflix-row">
        @foreach($ebooks as $book)
            <div class="book-card glass">
                @php
                    // Generate a deterministic hash based on the book title for the image seed
                    $seed = md5($book->title);
                @endphp
                <div class="book-cover {{ strtolower($book->file_type) }}" style="background-image: url('https://picsum.photos/seed/{{ $seed }}/140/195');">
                    <span title="{{ $book->title }}">{{ $book->title }}</span>
                </div>
                <div class="book-title" title="{{ $book->title }}">
                    {{ Str::limit($book->title, 35) }}
                </div>
                <div class="book-meta">
                    {{ $book->chapters_count }} Kapitel &bull; {{ strtoupper($book->file_type) }}
                </div>
                <div class="book-actions">
                    <a href="{{ route('ebooks.read', $book) }}" class="btn" style="width: 100%;">Lesen &rarr;</a>
                </div>
            </div>
        @endforeach
    </div>
@empty
    <div class="empty-state glass">
        <h3 style="font-size: 1.5rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Noch keine Bücher vorhanden</h3>
        <p class="book-meta" style="color:var(--text-muted); margin-top: 1rem; margin-bottom: 2rem;">Lade als Admin dein erstes Ebook hoch, um zu starten.</p>
        @auth
            <a href="{{ route('ebooks.create') }}" class="btn">+ Jetzt hochladen</a>
        @endauth
    </div>
@endforelse

@endsection
