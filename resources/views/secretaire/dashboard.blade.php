@extends('layouts.app')

@section('title', 'Dashboard Secrétaire')

@section('menu')
    <div class="menu-label">Principal</div>
    <a href="/secretaire/dashboard" class="menu-item active"><span>📊</span> Tableau de bord</a>

    <div class="menu-label">Secrétariat</div>
    <a href="/secretaire/eleves" class="menu-item"><span>🎒</span> Élèves</a>
    <a href="/secretaire/inscriptions" class="menu-item"><span>📋</span> Inscriptions</a>
    <a href="/secretaire/plaintes" class="menu-item"><span>📣</span> Plaintes</a>
    <a href="/secretaire/recus" class="menu-item"><span>🧾</span> Reçus</a>
@endsection

@section('page_title', 'Tableau de bord — Secrétaire')

@section('content')

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Élèves inscrits</div>
            <div style="font-size: 32px; font-weight: 700; color: #1a73e8; margin-top: 8px;">0</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Plaintes en cours</div>
            <div style="font-size: 32px; font-weight: 700; color: #fbbc04; margin-top: 8px;">0</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #666;">Reçus émis</div>
            <div style="font-size: 32px; font-weight: 700; color: #34a853; margin-top: 8px;">0</div>
        </div>

    </div>

    <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <h3 style="font-size: 16px; color: #333; margin-bottom: 16px;">👋 Bienvenue, {{ session('utilisateur_nom') }}</h3>
        <p style="color: #666; font-size: 14px; line-height: 1.6;">
            Vous êtes connecté en tant que <strong>Secrétaire</strong>.
            Vous pouvez gérer les inscriptions, les plaintes et les reçus.
        </p>
    </div>

@endsection