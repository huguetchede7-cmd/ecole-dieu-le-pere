@extends('layouts.app')

@section('title', 'Dashboard Comptable')

@section('menu')
    <div class="menu-label">Principal</div>
    <a href="/comptable/dashboard" class="menu-item active"><span>📊</span> Tableau de bord</a>

    <div class="menu-label">Comptabilité</div>
    <a href="/comptable/paiements" class="menu-item"><span>💰</span> Paiements</a>
    <a href="/comptable/types-frais" class="menu-item"><span>📋</span> Types de frais</a>
    <a href="/comptable/eleves" class="menu-item"><span>🎒</span> Élèves</a>
@endsection

@section('page_title', 'Tableau de bord — Comptable')

@section('content')

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Paiements ce mois</div>
            <div style="font-size: 32px; font-weight: 700; color: #1a73e8; margin-top: 8px;">0</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Total encaissé</div>
            <div style="font-size: 32px; font-weight: 700; color: #34a853; margin-top: 8px;">0 F</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Impayés</div>
            <div style="font-size: 32px; font-weight: 700; color: #ea4335; margin-top: 8px;">0</div>
        </div>

    </div>

    <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <h3 style="font-size: 16px; color: #333; margin-bottom: 16px;">👋 Bienvenue, {{ session('utilisateur_nom') }}</h3>
        <p style="color: #666; font-size: 14px; line-height: 1.6;">
            Vous êtes connecté en tant que <strong>Comptable</strong>.
            Vous pouvez gérer les paiements et les frais scolaires.
        </p>
    </div>

@endsection