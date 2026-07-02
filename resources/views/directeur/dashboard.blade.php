@extends('layouts.app')

@section('title', 'Dashboard Directeur')

@section('menu')
    <div class="menu-label">Principal</div>
    <a href="/directeur/dashboard" class="menu-item active"><span>📊</span> Tableau de bord</a>

    <div class="menu-label">Consultation</div>
    <a href="/directeur/eleves" class="menu-item"><span>🎒</span> Élèves</a>
    <a href="/directeur/classes" class="menu-item"><span>🏫</span> Classes</a>
    <a href="/directeur/notes" class="menu-item"><span>📝</span> Notes</a>
    <a href="/directeur/absences" class="menu-item"><span>📅</span> Absences</a>
    <a href="/directeur/paiements" class="menu-item"><span>💰</span> Paiements</a>
@endsection

@section('page_title', 'Tableau de bord — Directeur')

@section('content')

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Total Élèves</div>
            <div style="font-size: 32px; font-weight: 700; color: #1a73e8; margin-top: 8px;">0</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Total Classes</div>
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
            Vous êtes connecté en tant que <strong>Directeur</strong>. 
            Vous pouvez consulter les élèves, les classes, les notes, les absences et les paiements.
        </p>
    </div>

@endsection