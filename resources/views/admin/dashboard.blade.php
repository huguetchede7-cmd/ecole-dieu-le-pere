@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Tableau de bord')

@section('content')

<div style="background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
    <h3 style="font-size:16px; color:#333; margin-bottom:12px;">🎉 Bienvenue sur la plateforme</h3>
    <p style="color:#666; font-size:14px; line-height:1.8;">
        Vous êtes connecté en tant qu'<strong>Administrateur</strong>.
        Utilisez le menu à gauche pour gérer les élèves, les classes, les paiements, les notes et les absences de l'école <strong>Dieu le Père</strong>.
    </p>
</div>

<div style="background:white; border-radius:12px; padding:18px 24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #1a73e8; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between;">
    <div style="font-size:13px; color:#666;">📅 Année scolaire</div>
    <form method="GET" action="{{ route('admin.dashboard') }}">
        <select name="annee_scolaire" onchange="this.form.submit()"
            style="padding: 8px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; font-weight: 700; color: #1a73e8; background: #f0f6ff; cursor: pointer;">
            @foreach($anneesDisponibles as $annee)
            <option value="{{ $annee }}" {{ $annee === $anneeScolaire ? 'selected' : '' }}>{{ $annee }}</option>
            @endforeach
        </select>
    </form>
</div>

<div style="display:flex; gap:20px; margin-bottom:30px; flex-wrap:wrap;">

    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #1a73e8;">
        <div style="font-size:13px; color:#666; margin-bottom:8px;">👨‍🎓 Total Élèves</div>
        <div style="font-size:36px; font-weight:700; color:#1a73e8;">{{ App\Models\Eleve::count() }}</div>
    </div>

    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #34a853;">
        <div style="font-size:13px; color:#666; margin-bottom:8px;">🏫 Total Classes</div>
        <div style="font-size:36px; font-weight:700; color:#34a853;">{{ $totalClasses }}</div>
    </div>

    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #9334e6;">
        <div style="font-size:13px; color:#666; margin-bottom:8px;">👨‍🏫 Enseignants</div>
        <div style="font-size:36px; font-weight:700; color:#9334e6;">{{ $totalEnseignants }}</div>
    </div>

</div>

<div style="display:flex; gap:20px; margin-bottom:30px; flex-wrap:wrap;">

    <div style="flex:1; min-width:220px; background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #fbbc04;">
        <div style="font-size:13px; color:#666; margin-bottom:8px;">💵 Montant attendu</div>
        <div style="font-size:28px; font-weight:700; color:#333;">{{ number_format($montantAttenduTotal, 0, ',', ' ') }} FCFA</div>
    </div>

    <div style="flex:1; min-width:220px; background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #34a853;">
        <div style="font-size:13px; color:#666; margin-bottom:8px;">✅ Montant encaissé</div>
        <div style="font-size:28px; font-weight:700; color:#2e7d32;">{{ number_format($montantEncaisseTotal, 0, ',', ' ') }} FCFA</div>
    </div>

    <div style="flex:1; min-width:220px; background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #ea4335;">
        <div style="font-size:13px; color:#666; margin-bottom:8px;">⏳ Solde restant</div>
        <div style="font-size:28px; font-weight:700; color:#c62828;">{{ number_format($soldeRestant, 0, ',', ' ') }} FCFA</div>
    </div>

</div>

@if(count($alertesEcheances) > 0)
<div style="background:#fff8e1; border:1px solid #fbbc04; border-radius:12px; padding:20px 24px; margin-bottom:30px;">
    <h3 style="font-size:15px; color:#b26a00; margin-bottom:14px;">🔔 Échéances à surveiller</h3>

    @foreach($alertesEcheances as $alerte)
    <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-top:1px solid rgba(178,106,0,0.15);">
        <div>
            <div style="font-size:14px; font-weight:600; color:#333;">{{ $alerte['libelle'] }}</div>
            <div style="font-size:12px; color:#777;">
                {{ $alerte['est_depassee'] ? '⚠️ Dépassée le' : 'Avant le' }}
                {{ \Carbon\Carbon::parse($alerte['date_limite'])->format('d/m/Y') }}
                — {{ number_format($alerte['montant'], 0, ',', ' ') }} FCFA attendus
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:18px; font-weight:700; color:{{ $alerte['nb_eleves_en_retard'] > 0 ? '#c62828' : '#2e7d32' }};">
                {{ $alerte['nb_eleves_en_retard'] }}
            </div>
            <div style="font-size:11px; color:#999;">élève(s) sous le seuil</div>
        </div>
    </div>
    @endforeach
</div>
@endif

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden; margin-bottom:30px;">
    <div style="padding:20px 24px; border-bottom:1px solid #f0f0f0;">
        <h3 style="font-size:16px; color:#333;">⚠️ Élèves en situation de retard de paiement</h3>
    </div>

    @if(count($elevesEnRetard) > 0)
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f8f9fa;">
                <th style="padding:12px 24px; text-align:left; font-size:13px; color:#666; font-weight:600;">Élève</th>
                <th style="padding:12px 24px; text-align:left; font-size:13px; color:#666; font-weight:600;">Classe</th>
                <th style="padding:12px 24px; text-align:right; font-size:13px; color:#666; font-weight:600;">Solde dû</th>
            </tr>
        </thead>
        <tbody>
            @foreach($elevesEnRetard as $e)
            <tr style="border-top:1px solid #f0f0f0;">
                <td style="padding:12px 24px; font-size:14px; color:#333; font-weight:600;">{{ $e['nom'] }} {{ $e['prenom'] }}</td>
                <td style="padding:12px 24px; font-size:14px; color:#555;">{{ $e['classe'] }}</td>
                <td style="padding:12px 24px; text-align:right; font-size:14px; color:#c62828; font-weight:700;">{{ number_format($e['solde'], 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="padding:30px 24px; text-align:center; color:#999; font-size:14px;">
        Aucun élève en retard de paiement pour l'instant. 🎉
    </div>
    @endif
</div>

@endsection