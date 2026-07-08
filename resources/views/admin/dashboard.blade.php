@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('page_title', 'Tableau de bord')

@section('content')
<div style="display:flex; gap:20px; margin-bottom:30px; flex-wrap:wrap;">

    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #1a73e8;">
        <div style="font-size:13px; color:#666; margin-bottom:8px;">👨‍🎓 Total Élèves</div>
        <div style="font-size:36px; font-weight:700; color:#1a73e8;">{{ App\Models\Eleve::count() }}</div>
    </div>

    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #34a853;">
        <div style="font-size:13px; color:#666; margin-bottom:8px;">🏫 Total Classes</div>
        <div style="font-size:36px; font-weight:700; color:#34a853;">{{ App\Models\Classe::count() }}</div>
    </div>

    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #fbbc04;">
        <div style="font-size:13px; color:#666; margin-bottom:8px;">💰 Paiements ce mois</div>
        <div style="font-size:36px; font-weight:700; color:#fbbc04;">0</div>
    </div>

    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #ea4335;">
        <div style="font-size:13px; color:#666; margin-bottom:8px;">👥 Utilisateurs</div>
        <div style="font-size:36px; font-weight:700; color:#ea4335;">{{ App\Models\Utilisateur::count() }}</div>
    </div>

</div>

<div style="background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
    <h3 style="font-size:16px; color:#333; margin-bottom:12px;">🎉 Bienvenue sur la plateforme</h3>
    <p style="color:#666; font-size:14px; line-height:1.8;">
        Vous êtes connecté en tant qu'<strong>Administrateur</strong>. 
        Utilisez le menu à gauche pour gérer les élèves, les classes, les paiements, les notes et les absences de l'école <strong>Dieu le Père</strong>.
    </p>
</div>
@endsection