@extends('layouts.app')

@section('content')
<style>
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .admin-header h1 {
        margin: 0;
        font-weight: 700;
    }

    .table-container {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    th, td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--glass-border);
    }

    th {
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
        background: rgba(15, 23, 42, 0.4);
    }

    td {
        color: var(--text-light);
        vertical-align: middle;
    }
    
    tr:last-child td {
        border-bottom: none;
    }

    tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(59, 130, 246, 0.5);
    }

    .badge.pdf {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.5);
    }

    .badge.txt {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border-color: rgba(16, 185, 129, 0.5);
    }

    .actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }

    .btn-danger {
        background: transparent;
        color: #f87171;
        border: 1px solid #f87171;
    }

    .btn-danger:hover {
        background: #ef4444;
        color: white;
    }
    
    .genre-form {
        display: flex;
        gap: 0.5rem;
    }
    
    select.form-control {
        width: auto;
        padding: 0.4rem;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        border-radius: 6px;
        font-size: 0.85rem;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }
</style>

<div class="admin-header">
    <h1>Admin Panel - Bücherverwaltung</h1>
    <a href="{{ route('ebooks.create') }}" class="btn">+ Neues Buch hochladen</a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Titel</th>
                <th>Typ</th>
                <th>Kapitel</th>
                <th>Genre</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ebooks as $book)
                <tr>
                    <td style="font-weight: 600;">{{ Str::limit($book->title, 40) }}</td>
                    <td>
                        <span class="badge {{ strtolower($book->file_type) }}">{{ strtoupper($book->file_type) }}</span>
                    </td>
                    <td>{{ $book->chapters_count }}</td>
                    <td>
                        <form action="{{ route('admin.update-genre', $book) }}" method="POST" class="genre-form">
                            @csrf
                            @method('PUT')
                            <select name="genre" class="form-control" onchange="this.form.submit()">
                                @foreach($allGenres as $genre)
                                    <option value="{{ $genre }}" {{ $book->genre === $genre ? 'selected' : '' }}>
                                        {{ $genre }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="actions">
                        <a href="{{ route('ebooks.read', $book) }}" class="btn btn-sm" target="_blank" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">Ansehen</a>
                        
                        <form action="{{ route('admin.destroy', $book) }}" method="POST" onsubmit="return confirm('Möchtest du dieses Buch wirklich endgültig löschen? Die Datei wird physisch vom Server entfernt.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Löschen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            Bisher wurden keine Bücher in die Bibliothek geladen.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
