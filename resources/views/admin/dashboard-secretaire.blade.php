@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')

@section('content')

@include('partials.salutation')

<div style="display:flex; gap:16px; flex-wrap:wrap;">
    <a href="/admin/inscriptions" style="flex:1; min-width:220px; background:white; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); text-decoration:none; color:#333;">
        <div style="font-size:28px;">📝</div>
        <div style="font-weight:600; margin-top:8px;">Inscriptions</div>
        <div style="font-size:13px; color:#888; margin-top:4px;">Inscrire ou réinscrire un élève</div>
    </a>
    <a href="/admin/eleves" style="flex:1; min-width:220px; background:white; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); text-decoration:none; color:#333;">
        <div style="font-size:28px;">🎒</div>
        <div style="font-weight:600; margin-top:8px;">Élèves</div>
        <div style="font-size:13px; color:#888; margin-top:4px;">Consulter la liste des élèves</div>
    </a>
</div>

@endsection