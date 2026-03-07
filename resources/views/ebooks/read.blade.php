@extends('layouts.app')

@section('content')
<style>
    .reader-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 1.5rem 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .reader-header h1 {
        margin: 0;
        font-size: 1.5rem;
        flex: 1;
        min-width: 250px;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btn-back {
        background: rgba(255,255,255,0.05);
        color: var(--text-light);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
        border: 1px solid var(--glass-border);
    }

    .btn-back:hover {
        background: rgba(255,255,255,0.1);
    }

    .reader-container {
        width: 100%;
        min-height: 75vh;
        overflow: hidden;
    }

    .pdf-frame {
        width: 100%;
        height: 80vh;
        border: none;
        display: block;
        background: white; /* Required for some PDFs with transparent backgrounds */
    }

    .txt-reader {
        padding: 2.5rem 3rem;
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
        background: var(--primary-gradient);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2);
    }

    .pagination-controls .btn-page:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(249, 115, 22, 0.4);
    }

    .pagination-controls .btn-page.disabled {
        opacity: 0.4;
        pointer-events: none;
        box-shadow: none;
        transform: none;
    }

    @media (max-width: 768px) {
        .reader-header {
            padding: 1.5rem;
            flex-direction: column;
            align-items: flex-start;
        }
        .txt-reader {
            padding: 1.5rem;
            font-size: 1.05rem; /* Slightly larger base on mobile for readability */
            line-height: 1.7;
        }
        .pagination-controls {
            padding: 1rem;
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        .pagination-controls .btn-page {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="reader-header glass">
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

<div class="reader-container glass">
        @if(strtolower($ebook->file_type) === 'pdf')
            @guest 
                <div style="padding: 4rem 2rem; text-align: center;">
                    <h2 style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Leseprobe zu Ende</h2>
                    <p style="color: var(--text-muted);">PDF Dateien können nur von registrierten Benutzern gelesen werden.</p>
                    <a href="{{ route('register') }}" class="btn" style="margin-top: 1.5rem;">Jetzt kostenlos registrieren</a>
                </div>
            @else
                <iframe class="pdf-frame" src="{{ route('ebooks.stream', $ebook) }}#toolbar=0" title="PDF Reader"></iframe>
            @endguest
        @elseif(strtolower($ebook->file_type) === 'txt')
            
            @guest
                <div class="txt-reader" style="position: relative;">
                    {!! substr(strip_tags($textContent), 0, 800) !!}...
                    
                    <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 250px; background: linear-gradient(to bottom, transparent, var(--bg-color)); display: flex; align-items: flex-end; justify-content: center; padding-bottom: 2.5rem;">
                        <div class="glass" style="padding: 2rem; border-radius: 12px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.8); border: 1px solid rgba(249, 115, 22, 0.4); max-width: 80%;">
                            <h3 style="margin-top: 0; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 1.3rem;">Möchtest du weiterlesen?</h3>
                            <p style="color: var(--text-muted); margin-bottom: 2rem;">Registriere dich jetzt kostenlos, um das ganze Buch zu lesen.</p>
                            <a href="{{ route('register') }}" class="btn">Kostenlos registrieren</a>
                        </div>
                    </div>
                </div>
            @else
                @if($totalPages > 1)
                <div class="pagination-controls">
                    <a href="?page={{ $currentPage - 1 }}" class="btn-page {{ $currentPage <= 1 ? 'disabled' : '' }}">&larr; Vorherige Seite</a>
                    <span>Seite {{ $currentPage }} von {{ $totalPages }}</span>
                    <a href="?page={{ $currentPage + 1 }}" class="btn-page {{ $currentPage >= $totalPages ? 'disabled' : '' }}">Nächste Seite &rarr;</a>
                </div>
                @endif
                
                <div class="txt-reader" id="reader-content">{!! $textContent !!}</div>
                
                @if($totalPages > 1)
                <div class="pagination-controls" style="border-top: 1px solid var(--glass-border); border-bottom: none;">
                    <a href="?page={{ $currentPage - 1 }}" class="btn-page {{ $currentPage <= 1 ? 'disabled' : '' }}">&larr; Vorherige Seite</a>
                    <span>Seite {{ $currentPage }} von {{ $totalPages }}</span>
                    <a href="?page={{ $currentPage + 1 }}" class="btn-page {{ $currentPage >= $totalPages ? 'disabled' : '' }}">Nächste Seite &rarr;</a>
                </div>
                @endif
            @endguest
            
        @else
            <div style="padding: 2rem; text-align: center;">Format nicht unterstützt.</div>
        @endif
    </div>

    @auth
        <div style="margin-top: 1rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
            Bisher gelesene Zeit: <span id="time-spent">{{ floor(($readingSession->time_spent_seconds ?? 0) / 60) }}</span> Minuten
        </div>

        <script>
            // Only strictly track for TXT since it's easier to paginate/scroll
            let currentProgress = {{ $readingSession->progress ?? 0 }};
            let timeSpent = 0; // seconds spent in current session window
            const ebookId = {{ $ebook->id }};
            const updateInterval = 10000; // 10 seconds

            // Auto-scroll to previous position for TXT
            window.addEventListener('load', function() {
                @if(strtolower($ebook->file_type) === 'txt' && ($readingSession->progress ?? 0) > 0)
                    let maxScroll = document.body.scrollHeight - window.innerHeight;
                    let scrollTarget = maxScroll * (currentProgress / 100);
                    window.scrollTo({ top: scrollTarget, behavior: 'instant' });
                @endif
            });

            setInterval(() => {
                timeSpent += 10;
                
                // Update total time in UI
                const displayTotalSeconds = {{ $readingSession->time_spent_seconds ?? 0 }} + timeSpent;
                document.getElementById('time-spent').innerText = Math.floor(displayTotalSeconds / 60);

                // Calculate progress
                @if(strtolower($ebook->file_type) === 'txt')
                    let maxScroll = document.body.scrollHeight - window.innerHeight;
                    if (maxScroll <= 0) {
                        currentProgress = 100; // Small page
                    } else {
                        currentProgress = Math.max(currentProgress, Math.round((window.scrollY / maxScroll) * 100));
                        if(currentProgress > 100) currentProgress = 100;
                    }
                @elseif(strtolower($ebook->file_type) === 'pdf')
                    currentProgress = 100; // Can't easily track PDF internal scroll, mark as read
                @endif

                // Send progress to server
                fetch(`/ebooks/${ebookId}/progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        progress: currentProgress,
                        time_spent_seconds: 10
                    })
                });
            }, updateInterval);
        </script>
    @endauth
@endsection
