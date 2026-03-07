@extends('layouts.app')

@section('content')
<style>
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap; /* better for mobile */
        gap: 1rem;
    }
    
    .admin-header h1 {
        margin: 0;
        font-weight: 700;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .table-container {
        /* Using global .glass class will provide background/border/blur */
        border-radius: 12px;
        overflow-x: auto; /* horizontally scrollable */
        -webkit-overflow-scrolling: touch;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        min-width: 600px; /* Force minimum width to trigger scroll on small screens */
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
        background: rgba(15, 23, 42, 0.6);
    }

    td {
        color: var(--text-light);
        vertical-align: top;
    }
    
    tr:last-child td {
        border-bottom: none;
    }

    tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }

    .btn-danger {
        background: transparent;
        color: #f87171;
        border: 1px solid rgba(248, 113, 113, 0.5);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-danger:hover {
        background: rgba(239, 68, 68, 0.2);
        border-color: #f87171;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }

    .progress-list {
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 0.85rem;
    }
    
    .progress-list li {
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .progress-list li:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
</style>

<div class="admin-header">
    <h1>Admin Panel - Benutzerverwaltung</h1>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('admin.index') }}" class="btn btn-outline">Bücher</a>
        <a href="{{ route('admin.users') }}" class="btn">Nutzer</a>
        <a href="{{ route('admin.settings') }}" class="btn btn-outline">Einstellungen</a>
        <a href="{{ route('ebooks.create') }}" class="btn btn-outline">+ Hochladen</a>
    </div>
</div>

<div class="table-container glass">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>E-Mail</th>
                <th>Registriert am</th>
                <th>Leseverlauf & Fortschritt</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td style="font-weight: 600;">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if($user->readingSessions->count() > 0)
                            <ul class="progress-list">
                                @foreach($user->readingSessions as $session)
                                    @if($session->ebook)
                                        <li>
                                            <strong>{{ Str::limit($session->ebook->title, 35) }}</strong><br>
                                            <span style="color: var(--primary);">Fortschritt: {{ $session->progress }}%</span> | 
                                            <span style="color: var(--text-muted);">Lesezeit: {{ floor($session->time_spent_seconds / 60) }} Min.</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                            <div style="margin-top: 1rem; font-weight: 600; font-size: 0.85rem;">
                                Gesamte Lesezeit: {{ floor($user->readingSessions->sum('time_spent_seconds') / 60) }} Minuten
                            </div>
                        @else
                            <span style="color: var(--text-muted);">Noch keine Bücher gelesen.</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Möchtest du diesen Benutzer unwiderruflich löschen? Alle Daten und Lesefortschritte gehen verloren.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger btn-sm">Konto löschen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            Es haben sich noch keine Benutzer registriert.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
