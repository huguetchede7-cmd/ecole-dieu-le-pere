@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')

@section('content')

@include('partials.salutation')

@if($correctionsEnAttente->count() > 0)
<div style="background:#fff4e5; border-radius:12px; padding:20px 24px; margin-bottom:20px; border-left:4px solid #e65100;">
    <h3 style="font-size:16px; color:#e65100; margin-bottom:10px;">
        ✏️ {{ $correctionsEnAttente->count() }} correction{{ $correctionsEnAttente->count() > 1 ? 's' : '' }} de notes en attente de votre validation
    </h3>

    @foreach($correctionsEnAttente->take(5) as $c)
    <div style="font-size:14px; color:#555; margin-top:8px; line-height:1.7;">
        <strong>{{ $c->note->eleve->nom }} {{ $c->note->eleve->prenom }}</strong>
        · {{ $c->note->matiere->nom ?? '' }} ({{ $c->note->periode }}) :
        {{ (float) $c->ancienne_note }}/{{ (float) $c->ancienne_note_max }}
        → <strong>{{ (float) $c->nouvelle_note }}/{{ (float) $c->nouvelle_note_max }}</strong><br>
        <span style="font-size:13px; color:#777;">
            Proposé par {{ $c->auteur ? ucfirst($c->auteur->role) . ' ' . $c->auteur->nom : 'utilisateur supprimé' }}
            le {{ $c->created_at->timezone('Africa/Porto-Novo')->format('d/m/Y à H:i') }}
            @if($c->motif) · motif : <em>« {{ $c->motif }} »</em> @endif
        </span>
    </div>
    @endforeach

    @if($correctionsEnAttente->count() > 5)
        <div style="font-size:13px; color:#777; margin-top:8px;">… et {{ $correctionsEnAttente->count() - 5 }} autre(s).</div>
    @endif

    <a href="/admin/notes/corrections" style="display:inline-block; margin-top:14px; background:#e65100; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">
        Examiner et valider →
    </a>
</div>
@endif

@if($classe)
<div style="background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:20px;">
    <h3 style="font-size:16px; color:#333; margin-bottom:10px;">🏫 Votre classe</h3>
    <p style="color:#666; font-size:14px; line-height:1.8;">
        Bienvenue dans la classe de <strong>{{ $classe->nom }}</strong>{{ $classe->niveau ? ' (' . $classe->niveau . ')' : '' }}.
        Vous avez <strong>{{ $nbEleves }}</strong> élève{{ $nbEleves > 1 ? 's' : '' }} inscrit{{ $nbEleves > 1 ? 's' : '' }}.
    </p>
</div>

<div style="display:flex; gap:16px; flex-wrap:wrap;">
    <a href="/admin/absences" style="flex:1; min-width:220px; background:white; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); text-decoration:none; color:#333;">
        <div style="font-size:28px;">📅</div>
        <div style="font-weight:600; margin-top:8px;">Absences</div>
        <div style="font-size:13px; color:#888; margin-top:4px;">Faire l'appel et consulter les présences</div>
    </a>
    <a href="/admin/notes" style="flex:1; min-width:220px; background:white; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); text-decoration:none; color:#333;">
        <div style="font-size:28px;">📝</div>
        <div style="font-weight:600; margin-top:8px;">Évaluations et bulletins</div>
        <div style="font-size:13px; color:#888; margin-top:4px;">Saisir les notes et éditer les bulletins</div>
    </a>
</div>
@else
<div style="background:#fdecea; color:#c62828; border-radius:12px; padding:20px; font-size:14px;">
    Aucune classe ne vous est encore affectée. Contactez l'administration.
</div>
@endif

@endsection
