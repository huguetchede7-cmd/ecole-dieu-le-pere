@extends('layouts.app')

@section('title', 'Notes de l\'élève')
@section('page_title', 'Notes de ' . $eleve->nom . ' ' . $eleve->prenom)

@section('content')

@if(session('success'))
<div style="background:#e6f4ea; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
✅ {{ session('success') }}
</div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <h2 style="font-size:16px; color:#333;">Année scolaire {{ $annee }}</h2>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('admin.notes.bulletin', ['eleve' => $eleve->id, 'annee_scolaire' => $annee]) }}" style="background:#1a73e8; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">Voir le bulletin</a>
        <a href="/admin/notes" style="background:#f0f0f0; color:#333; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px;">Retour</a>
    </div>
</div>

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f8f9fa;">
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Matière</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Trimestre</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Note</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Saisie par</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($notes as $n)
        <tr style="border-top:1px solid #f0f0f0;">
            <td style="padding:14px 20px; font-size:14px; font-weight:600; color:#333;">{{ $n->matiere->nom ?? 'N/A' }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $n->periode }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#333; font-weight:600;">
                {{ (float) $n->note }}/{{ (float) $n->note_max }}
                @if($n->corrections->count())
                    <span style="background:#fff4e5; color:#e65100; padding:3px 8px; border-radius:20px; font-size:11px; font-weight:600; margin-left:6px;">Corrigée</span>
                @endif
            </td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $n->enseignant->nom ?? '-' }}</td>
            <td style="padding:14px 20px;">
                <a href="/admin/notes/{{ $n->id }}/edit" style="background:#1a73e8; color:white; padding:6px 14px; border-radius:6px; font-size:12px; text-decoration:none; font-weight:600;">Corriger</a>
            </td>
        </tr>
        @if($n->corrections->count())
        <tr style="background:#fffaf2;">
            <td colspan="5" style="padding:10px 20px 14px 40px;">
                @foreach($n->corrections as $c)
                <div style="font-size:13px; color:#555; margin-top:4px;">
                    ✏️ <strong>{{ (float) $c->ancienne_note }}/{{ (float) $c->ancienne_note_max }}</strong>
                    → <strong>{{ (float) $c->nouvelle_note }}/{{ (float) $c->nouvelle_note_max }}</strong>
                    · par {{ $c->auteur ? ucfirst($c->auteur->role) . ' ' . $c->auteur->nom : 'utilisateur supprimé' }}
                    · le {{ $c->created_at->timezone('Africa/Porto-Novo')->format('d/m/Y à H:i') }}
                    @if($c->motif) · <em>« {{ $c->motif }} »</em> @endif
                </div>
                @endforeach
            </td>
        </tr>
        @endif
        @empty
        <tr>
            <td colspan="5" style="padding:40px; text-align:center; color:#999; font-size:14px;">Aucune note pour cette année.</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

@endsection