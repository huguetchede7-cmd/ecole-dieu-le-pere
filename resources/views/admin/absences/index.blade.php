@extends('layouts.app')

@section('title', 'Absences')
@section('page_title', 'Gestion des Absences')

@section('content')

@if(session('success'))
<div style="background:#e6f4ea; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
✅ {{ session('success') }}
</div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <h2 style="font-size:16px; color:#333;">Consulter la présence d'un jour</h2>
    <a href="{{ route('admin.absences.create') }}" style="background:#1a73e8; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">
        + Faire l'appel
    </a>
</div>

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); padding:24px; margin-bottom:24px;">
    <form method="GET" action="{{ route('admin.absences.index') }}" style="display:flex; gap:16px; align-items:flex-end;">
        <div style="flex:1;">
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Classe</label>
            <select name="classe_id" required style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                <option value="">-- Choisir --</option>
                @foreach($classes as $classe)
                <option value="{{ $classe->id }}" {{ $classeId == $classe->id ? 'selected' : '' }}>
                    {{ $classe->nom }} {{ $classe->niveau ? '(' . $classe->niveau . ')' : '' }}
                </option>
                @endforeach
            </select>
        </div>

        <div style="flex:1;">
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Date</label>
            <input type="date" name="date" value="{{ $date ?? date('Y-m-d') }}" required
                style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
        </div>

        <button type="submit" style="background:#1a73e8; color:white; border:none; padding:10px 24px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
            Rechercher
        </button>
    </form>
</div>

@if($classeId && $date)
<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f8f9fa;">
                <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Élève</th>
                <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Statut</th>
                <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Motif</th>
                <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Enregistré par</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resultats as $r)
            <tr style="border-top:1px solid #f0f0f0;">
                <td style="padding:14px 20px; font-size:14px; font-weight:600; color:#333;">{{ $r['nom'] }} {{ $r['prenom'] }}</td>
                <td style="padding:14px 20px;">
                    @if($r['statut'] === 'present')
                    <span style="background:#e6f4ea; color:#2e7d32; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">Présent</span>
                    @elseif($r['statut'] === 'absent')
                    <span style="background:#fdecea; color:#c62828; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">Absent</span>
                    @elseif($r['statut'] === 'retard')
                    <span style="background:#fff4e5; color:#b26a00; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">Retard</span>
                    @else
                    <span style="background:#f0f0f0; color:#999; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">Non renseigné</span>
                    @endif
                </td>
                <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $r['motif'] ?? '—' }}</td>
                <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $r['enseignant'] ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="padding:40px; text-align:center; color:#999; font-size:14px;">
                    Aucun élève trouvé pour cette classe.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@endsection