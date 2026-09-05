@extends('layouts.app')

@section('title', 'Notes')
@section('page_title', 'Gestion des Notes')

@section('content')

@if(session('success'))
<div style="background:#e6f4ea; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
✅ {{ session('success') }}
</div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <h2 style="font-size:16px; color:#333;">Élèves notés</h2>
    <a href="{{ route('admin.notes.create') }}" style="background:#1a73e8; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">
        + Ajouter des notes
    </a>
</div>

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f8f9fa;">
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Élève</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Matricule</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Notes enregistrées</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Année scolaire</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($eleves as $eleve)
        <tr style="border-top:1px solid #f0f0f0;">
            <td style="padding:14px 20px; font-size:14px; font-weight:600; color:#333;">{{ $eleve['nom'] }} {{ $eleve['prenom'] }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $eleve['matricule'] }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $eleve['nb_notes'] }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $eleve['derniere_annee'] }}</td>
            <td style="padding:14px 20px;">
                <a href="{{ route('admin.notes.bulletin', ['eleve' => $eleve['id'], 'annee_scolaire' => $eleve['derniere_annee']]) }}"
                    style="background:#1a73e8; color:white; padding:6px 14px; border-radius:6px; font-size:12px; text-decoration:none; font-weight:600;">
                    Voir ses notes
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="padding:40px; text-align:center; color:#999; font-size:14px;">
                Aucune note enregistrée pour l'instant.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

@endsection