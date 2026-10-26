@extends('layouts.app')

@section('title', 'Examens et compositions')
@section('page_title', 'Examens et compositions')

@section('content')

@if(session('success'))
<div style="background:#e6f4ea; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
✅ {{ session('success') }}
</div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <h2 style="font-size:16px; color:#333;">Sessions de l'année {{ $annee }}</h2>
    <a href="/admin/examens/create" style="background:#1a73e8; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">
        + Nouvelle session
    </a>
</div>

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f8f9fa;">
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Session</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Dépôt des épreuves avant le</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Composition</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Saisie des notes avant le</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Phase</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($examens as $e)
        @php [$libelle, $couleur, $fond] = $e->phase(); @endphp
        <tr style="border-top:1px solid #f0f0f0;">
            <td style="padding:14px 20px; font-size:14px; color:#333;">
                <strong>{{ $e->titre }}</strong><br>
                <span style="font-size:12px; color:#888;">{{ $e->type }} · {{ $e->periode }}</span>
            </td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $e->date_limite_depot->format('d/m/Y') }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">du {{ $e->date_debut->format('d/m/Y') }}<br>au {{ $e->date_fin->format('d/m/Y') }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $e->date_limite_notes->format('d/m/Y') }}</td>
            <td style="padding:14px 20px;">
                <span style="background:{{ $fond }}; color:{{ $couleur }}; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;">{{ $libelle }}</span>
            </td>
            <td style="padding:14px 20px;">
                <a href="/admin/examens/{{ $e->id }}/edit" style="background:#1a73e8; color:white; padding:6px 14px; border-radius:6px; font-size:12px; text-decoration:none; font-weight:600;">Modifier</a>
                @if(session('utilisateur_role') !== 'directeur')
                <form method="POST" action="/admin/examens/{{ $e->id }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Supprimer cette session ?')" style="background:#ea4335; color:white; border:none; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer; margin-left:4px;">Supprimer</button>
                </form>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="padding:40px; text-align:center; color:#999; font-size:14px;">Aucune session pour cette année. Créez-en une avec « + Nouvelle session ».</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

@endsection
