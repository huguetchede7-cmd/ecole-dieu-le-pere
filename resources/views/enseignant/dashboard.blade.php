@extends('layouts.app')

@section('title', 'Dashboard Enseignant')

@section('menu')
    <div class="menu-label">Principal</div>
    <a href="/enseignant/dashboard" class="menu-item active"><span>📊</span> Tableau de bord</a>

    <div class="menu-label">Mon travail</div>
    <a href="/enseignant/notes" class="menu-item"><span>📝</span> Saisir les notes</a>
    <a href="/enseignant/absences" class="menu-item"><span>📅</span> Absences</a>
    <a href="/enseignant/eleves" class="menu-item"><span>🎒</span> Mes élèves</a>
@endsection

@section('page_title', 'Tableau de bord — Enseignant')

@section('content')

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Mes élèves</div>
            <div style="font-size: 32px; font-weight: 700; color: #1a73e8; margin-top: 8px;">0</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Notes saisies</div>
            <div style="font-size: 32px; font-weight: 700; color: #34a853; margin-top: 8px;">0</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Absences ce mois</div>
            <div style="font-size: 32px; font-weight: 700; color: #ea4335; margin-top: 8px;">0</div>
        </div>

    </div>

    <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <h3 style="font-size: 16px; color: #333; margin-bottom: 16px;">👋 Bienvenue, {{ session('utilisateur_nom') }}</h3>
        <p style="color: #666; font-size: 14px; line-height: 1.6;">
            Vous êtes connecté en tant qu'<strong>Enseignant</strong>.
            Vous pouvez saisir les notes et gérer les absences de vos élèves.
        </p>
    </div>

@endsection