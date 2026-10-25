@extends('layouts.app')

@section('title', 'Corrections de notes')
@section('page_title', 'Corrections de notes')

@section('content')

@php $estEnseignant = session('utilisateur_role') === 'enseignant'; @endphp

@if(session('success'))
<div style="background:#e6f4ea; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
✅ {{ session('success') }}
</div>
@endif

@if(session('erreur'))
<div style="background:#fdecea; color:#c62828; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
❌ {{ session('erreur') }}
</div>
@endif

@if($errors->any())
<div style="background:#fdecea; color:#c62828; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
    @foreach($errors->all() as $error)
    <div>❌ {{ $error }}</div>
    @endforeach
</div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2 style="font-size:16px; color:#333;">
        {{ $estEnseignant ? 'Corrections à valider' : 'Corrections en attente de validation' }}
        ({{ $enAttente->count() }})
    </h2>
    <a href="/admin/notes" style="background:#f0f0f0; color:#333; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px;">Retour aux notes</a>
</div>

@forelse($enAttente as $c)
<div style="background:white; border-radius:12px; padding:20px 24px; margin-bottom:16px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #e65100;">
    <div style="font-size:15px; font-weight:600; color:#333;">
        {{ $c->note->eleve->nom }} {{ $c->note->eleve->prenom }}
        <span style="font-weight:400; color:#777;">· {{ $c->note->matiere->nom ?? '' }} · {{ $c->note->periode }} · {{ $c->note->annee_scolaire }}</span>
    </div>

    <div style="font-size:22px; font-weight:700; margin:10px 0; color:#333;">
        {{ (float) $c->ancienne_note }}/{{ (float) $c->ancienne_note_max }}
        <span style="color:#e65100;">→</span>
        {{ (float) $c->nouvelle_note }}/{{ (float) $c->nouvelle_note_max }}
    </div>

    <div style="font-size:13px; color:#555; line-height:1.8;">
        Proposée par <strong>{{ $c->auteur ? ucfirst($c->auteur->role) . ' ' . $c->auteur->nom : 'utilisateur supprimé' }}</strong>
        le {{ $c->created_at->timezone('Africa/Porto-Novo')->format('d/m/Y à H:i') }}
        ({{ $c->created_at->locale('fr')->diffForHumans() }})<br>
        Motif : <em>« {{ $c->motif ?: 'non précisé' }} »</em>
    </div>

    @if($estEnseignant)
    <div style="display:flex; gap:12px; align-items:flex-start; flex-wrap:wrap; margin-top:16px;">
        <form method="POST" action="{{ route('admin.notes.corrections.confirmer', $c->id) }}">
            @csrf
            <button type="submit" onclick="return confirm('Confirmer cette correction ? La nouvelle note deviendra la note officielle.')"
                style="background:#2e7d32; color:white; border:none; padding:10px 20px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                ✅ Confirmer
            </button>
        </form>

        <form method="POST" action="{{ route('admin.notes.corrections.refuser', $c->id) }}" style="display:flex; gap:8px; flex:1; min-width:280px;">
            @csrf
            <input type="text" name="motif_refus" placeholder="Raison du refus" required
                style="flex:1; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
            <button type="submit" style="background:#ea4335; color:white; border:none; padding:10px 20px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                Refuser
            </button>
        </form>
    </div>
    @else
    <div style="margin-top:12px; font-size:13px; color:#e65100; font-weight:600;">
        En attente de l'enseignant{{ $c->validateur ? ' (' . $c->validateur->nom . ')' : '' }}
    </div>
    @endif
</div>
@empty
<div style="background:white; border-radius:12px; padding:40px; text-align:center; color:#999; font-size:14px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:24px;">
    Aucune correction en attente.
</div>
@endforelse

<h2 style="font-size:16px; color:#333; margin:32px 0 16px;">Historique récent</h2>

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f8f9fa;">
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Élève</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Matière</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Changement</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Proposée par</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Décision</th>
        </tr>
    </thead>
    <tbody>
        @forelse($traitees as $c)
        <tr style="border-top:1px solid #f0f0f0;">
            <td style="padding:14px 20px; font-size:14px; font-weight:600; color:#333;">{{ $c->note->eleve->nom }} {{ $c->note->eleve->prenom }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $c->note->matiere->nom ?? '' }} ({{ $c->note->periode }})</td>
            <td style="padding:14px 20px; font-size:14px; color:#333;">
                {{ (float) $c->ancienne_note }}/{{ (float) $c->ancienne_note_max }} → <strong>{{ (float) $c->nouvelle_note }}/{{ (float) $c->nouvelle_note_max }}</strong>
            </td>
            <td style="padding:14px 20px; font-size:13px; color:#555;">
                {{ $c->auteur ? ucfirst($c->auteur->role) . ' ' . $c->auteur->nom : 'utilisateur supprimé' }}<br>
                <span style="color:#999;">{{ $c->created_at->timezone('Africa/Porto-Novo')->format('d/m/Y H:i') }}</span>
                @if($c->motif)<br><em>« {{ $c->motif }} »</em>@endif
            </td>
            <td style="padding:14px 20px; font-size:13px;">
                @if($c->statut === 'refusee')
                    <span style="color:#c62828; font-weight:600;">Refusée</span>
                    @if($c->motif_refus)<br><em style="color:#555;">« {{ $c->motif_refus }} »</em>@endif
                @else
                    <span style="color:#2e7d32; font-weight:600;">Confirmée</span>
                @endif
                @if($c->decide_le)
                    <br><span style="color:#999;">{{ $c->decide_le->timezone('Africa/Porto-Novo')->format('d/m/Y H:i') }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="padding:30px; text-align:center; color:#999; font-size:14px;">Aucune correction traitée pour l'instant.</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

@endsection
