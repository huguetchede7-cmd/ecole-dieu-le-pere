@extends('layouts.app')

@section('title', 'Détail du reçu')
@section('page_title', 'Reçu ' . $recu->numero_recu)

@section('content')
<div style="max-width: 550px; background: white; border-radius: 12px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

    <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1a73e8; padding-bottom: 20px;">
        <h2 style="font-size: 20px; color: #1a73e8;">🏫 École Dieu le Père</h2>
        <p style="font-size: 13px; color: #666; margin-top: 4px;">Reçu d'inscription et de paiement</p>
    </div>

    <div style="margin-bottom: 16px;">
        <span style="font-size: 12px; color: #999;">N° Reçu</span>
        <p style="font-size: 16px; font-weight: 700; color: #333;">{{ $recu->numero_recu }}</p>
    </div>

    <div style="margin-bottom: 16px;">
<span style="font-size: 12px; color: #999;">Élève</span>
<p style="font-size: 15px; color: #333;">{{ $recu->inscription->eleve->nom ?? 'N/A' }} {{ $recu->inscription->eleve->prenom ?? '' }}</p>
</div>

<div style="margin-bottom: 16px;">
<span style="font-size: 12px; color: #999;">Matricule</span>
<p style="font-size: 15px; color: #333;">{{ $recu->inscription->eleve->matricule ?? 'N/A' }}</p>
</div>

    <div style="margin-bottom: 16px;">
        <span style="font-size: 12px; color: #999;">Classe / Groupe</span>
        <p style="font-size: 15px; color: #333;">{{ $recu->inscription->classe->nom ?? 'N/A' }}</p>
    </div>

    <div style="margin-bottom: 20px;">
        <span style="font-size: 12px; color: #999;">Année scolaire</span>
        <p style="font-size: 15px; color: #333;">{{ $recu->inscription->annee_scolaire ?? 'N/A' }}</p>
    </div>

    <div style="border-top: 1px solid #f0f0f0; padding-top: 16px; margin-bottom: 16px;">
        <span style="font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600;">Détail des paiements</span>

        @forelse($recu->paiements as $paiement)
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f5f5f5; font-size: 14px;">
                <span style="color: #333;">{{ $paiement->typeFrais->libelle ?? 'N/A' }}</span>
                <span style="font-weight: 600; color: #333;">{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</span>
            </div>
        @empty
            <p style="font-size: 14px; color: #ea4335; margin-top: 10px;">Aucun paiement effectué à ce jour.</p>
        @endforelse
    </div>

    @if($recu->paiements->isNotEmpty())
        <div style="display: flex; justify-content: space-between; padding: 12px 0; margin-bottom: 20px; font-size: 16px;">
            <span style="font-weight: 700; color: #333;">Total payé</span>
            <span style="font-weight: 700; color: #1a73e8;">{{ number_format($recu->montant_total, 0, ',', ' ') }} FCFA</span>
        </div>
    @endif

    <div style="margin-bottom: 16px;">
        <span style="font-size: 12px; color: #999;">Date d'émission</span>
        <p style="font-size: 15px; color: #333;">{{ $recu->date_emission }}</p>
    </div>

    <div style="margin-bottom: 24px;">
        <span style="font-size: 12px; color: #999;">Émis par</span>
        <p style="font-size: 15px; color: #333;">{{ $recu->secretaire->nom ?? 'N/A' }}</p>
    </div>

    <div style="display: flex; gap: 12px;">
        <a href="/admin/recus" style="background: #f0f0f0; color: #333; padding: 10px 20px; border-radius: 8px; font-size: 14px; text-decoration: none;">
            ← Retour
        </a>
        <button onclick="window.print()" style="background: #1a73e8; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; cursor: pointer;">
            🖨️ Imprimer
        </button>
    </div>

</div>
@endsection