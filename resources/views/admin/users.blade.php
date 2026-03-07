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
                    <td style="font-weight: 600;">
                        {{ $user->name }}
                        @if($user->is_admin)
                            <span class="badge" style="margin-left: 0.5rem; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border-color: rgba(245, 158, 11, 0.5);">Admin</span>
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if(!$user->is_admin && $user->readingSessions->count() > 0)
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
                        @elseif($user->is_admin)
                            <span style="color: var(--text-muted);">Admins haben kein Fortschritts-Tracking.</span>
                        @else
                            <span style="color: var(--text-muted);">Noch keine Bücher gelesen.</span>
                        @endif
                    </td>
                    <td style="display: flex; gap: 0.5rem; align-items: flex-start;">
                        <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @if($user->is_admin)
                                <button type="submit" class="btn btn-sm btn-outline" style="border-color: #f59e0b; color: #f59e0b;" 
                                    {{ $adminCount <= 1 ? 'disabled title="Der letzte Admin kann nicht entfernt werden"' : '' }}
                                    onclick="return confirm('Möchtest du diesem Benutzer wirklich die Admin-Rechte entziehen?');">
                                    Admin entfernen
                                </button>
                            @else
                                <button type="submit" class="btn btn-sm" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.5);" onclick="return confirm('Möchtest du diesen Benutzer zum Admin ernennen?');">
                                    Zum Admin ernennen
                                </button>
                            @endif
                        </form>

                        @if(!$user->is_admin)
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Möchtest du diesen Benutzer unwiderruflich löschen? Alle Daten und Lesefortschritte gehen verloren.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger btn-sm">Konto löschen</button>
                        </form>
                        @endif
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
