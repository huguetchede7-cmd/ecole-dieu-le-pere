@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')

@section('content')

@include('partials.salutation')

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