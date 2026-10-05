@extends('layouts.app')

@section('title', 'Détail du reçu')
@section('page_title', 'Reçu ' . $recu->numero_recu)

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #zone-impression, #zone-impression * {
        visibility: visible;
    }
    #zone-impression {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 0;
    }
    #bouton-imprimer, #bouton-retour {
        display: none !important;
    }
    .recu-copie {
    height: 118mm;
    max-height: 118mm;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    padding: 10px 36px !important;
    overflow: hidden;
    box-sizing: border-box;
    page-break-inside: avoid;
}
    .recu-copie h2 { font-size: 14px !important; margin-bottom: 0 !important; }
    .recu-copie p { font-size: 10px !important; margin-bottom: 1px !important; }
    .recu-copie .label-signature { margin-bottom: 14px !important; }
    .recu-copie .ligne-signature { margin-top: 6px !important; }
    @page {
        size: A4;
        margin: 5mm;
    }
}
</style>
@endpush

@section('content')

<div style="max-width: 650px; margin-bottom: 20px; display: flex; gap: 12px;">
    <a href="/admin/recus" id="bouton-retour" style="background: #f0f0f0; color: #333; padding: 10px 20px; border-radius: 8px; font-size: 14px; text-decoration: none;">
        ← Retour
    </a>
    <button id="bouton-imprimer" onclick="window.print()" style="background: #1a73e8; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; cursor: pointer;">
        🖨️ Imprimer
    </button>
</div>

<div id="zone-impression" style="max-width: 750px;">

@for($i = 0; $i < 2; $i++)

<div style="position: relative; overflow: hidden; background: white; border-radius: 12px; padding: 24px 36px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: {{ $i === 0 ? '0' : '20px' }};">


<div class="recu-copie" style="position: relative; overflow: hidden; background: white; border-radius: 12px; padding: 24px 36px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: {{ $i === 0 ? '0' : '20px' }};">
    <img src="{{ asset('images/logo-ecole-dieu-le-pere.svg') }}" alt="" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 260px; opacity: 0.08; z-index: 0; pointer-events: none;">

    <div style="position: relative; z-index: 1;">

        <div style="margin-bottom: 16px; border-bottom: 2px solid #1a73e8; padding-bottom: 12px;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;">

                <img src="{{ asset('images/armoiries-benin.png') }}" alt="Armoiries du Bénin" style="width: 45px; height: auto; margin-top: 4px;">

                <div style="text-align: center; flex: 1;">
                    <p style="font-size: 10px; color: #333; margin-bottom: 1px;">RÉPUBLIQUE DU BÉNIN</p>
                    <p style="font-size: 8px; color: #666; margin-bottom: 4px;">Ministère des Enseignements Maternels et Primaires</p>

                    <h2 style="font-size: 13px; color: #1a73e8; margin-bottom: 1px;">École Primaire Privée Dieu le Père</h2>
                    <p style="font-size: 8px; color: #666; margin-bottom: 1px;">Autorisation N° 049/MEMP/CAB/DC/SGM/DPP/DGS/DEPES/SA/2005</p>
                    <p style="font-size: 8px; color: #666; margin-bottom: 4px;">Tél: 23767777 — BP: 08 Parakou — dieulepere@yahoo.fr</p>

                    <p style="font-size: 11px; color: #666;">Reçu d'inscription et de paiement {{ $i === 0 ? '(Archive école)' : '(Copie parent)' }}</p>
                </div>

                <img src="{{ asset('images/logo-ecole-dieu-le-pere.svg') }}" alt="Logo de l'école" style="width: 45px; height: auto; margin-top: 4px;">

            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; margin-bottom: 12px;">
            <div>
                <span style="font-size: 10px; color: #999;">N° Reçu</span>
                <p style="font-size: 13px; font-weight: 700; color: #333;">{{ $recu->numero_recu }}</p>
            </div>
            <div>
                <span style="font-size: 10px; color: #999;">Date d'émission</span>
                <p style="font-size: 13px; color: #333;">{{ \Carbon\Carbon::parse($recu->date_emission)->format('d/m/Y') }}</p>
            </div>
            <div>
                <span style="font-size: 10px; color: #999;">Élève</span>
                <p style="font-size: 13px; color: #333;">{{ $recu->inscription->eleve->nom ?? 'N/A' }} {{ $recu->inscription->eleve->prenom ?? '' }}</p>
            </div>
            <div>
                <span style="font-size: 10px; color: #999;">Matricule</span>
                <p style="font-size: 13px; color: #333;">{{ $recu->inscription->eleve->matricule ?? 'N/A' }}</p>
            </div>
            <div>
                <span style="font-size: 10px; color: #999;">Classe</span>
                <p style="font-size: 13px; color: #333;">{{ $recu->inscription->classe->nom ?? 'N/A' }}</p>
            </div>
            <div>
                <span style="font-size: 10px; color: #999;">Année scolaire</span>
                <p style="font-size: 13px; color: #333;">{{ $recu->inscription->annee_scolaire ?? 'N/A' }}</p>
            </div>
        </div>

        <div style="border-top: 1px solid #f0f0f0; padding-top: 10px; margin-bottom: 10px;">
            <span style="font-size: 10px; color: #999; text-transform: uppercase; font-weight: 600;">Détail des paiements</span>

            @forelse($recu->paiements as $paiement)
            <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f5f5f5; font-size: 12px;">
                <span style="color: #333;">{{ $paiement->typeFrais->libelle ?? 'N/A' }}</span>
                <span style="font-weight: 600; color: #333;">{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</span>
            </div>
            @empty
            <p style="font-size: 12px; color: #ea4335; margin-top: 6px;">Aucun paiement effectué à ce jour.</p>
            @endforelse
        </div>

        @if($recu->paiements->isNotEmpty())
<div style="display: flex; justify-content: space-between; padding: 8px 0; margin-bottom: 4px; font-size: 14px;">
    <span style="font-weight: 700; color: #333;">Total payé</span>
    <span style="font-weight: 700; color: #1a73e8;">{{ number_format($recu->montant_total, 0, ',', ' ') }} FCFA</span>
</div>

@php
$niveauEleve = $recu->inscription->classe->niveau ?? null;
$soldeTotal = 0;

foreach ($recu->paiements as $paiement) {
    if (!$paiement->typeFrais) continue;

    $montantDu = $paiement->typeFrais->montantPour($niveauEleve) ?? 0;

    $dejaPaye = \App\Models\Paiement::where('eleve_id', $recu->inscription->eleve_id ?? null)
        ->where('type_frais_id', $paiement->type_frais_id)
        ->sum('montant_paye');

    $soldeTotal += max(0, $montantDu - $dejaPaye);
}
@endphp

<div style="display: flex; justify-content: space-between; padding: 8px 0; margin-bottom: 10px; font-size: 13px; background: {{ $soldeTotal > 0 ? '#fdecea' : '#e6f4ea' }}; padding: 8px 12px; border-radius: 6px;">
    <span style="font-weight: 600; color: {{ $soldeTotal > 0 ? '#c62828' : '#2e7d32' }};">Montant restant dû</span>
    <span style="font-weight: 700; color: {{ $soldeTotal > 0 ? '#c62828' : '#2e7d32' }};">{{ number_format($soldeTotal, 0, ',', ' ') }} FCFA</span>
</div>
@endif

<div style="display: flex; justify-content: space-between; font-size: 11px; color: #999; margin-bottom: 20px;">
    <span>Émis par : {{ $recu->secretaire->nom ?? 'N/A' }}</span>
</div>

<div class="ligne-signature" style="display: flex; justify-content: space-between; margin-top: 10px;">
    <div style="text-align: center; width: 45%;">
        <div class="label-signature" style="font-size: 10px; color: #999; margin-bottom: 30px;">Signature et cachet du Comptable</div>
        <div style="border-top: 1px solid #ccc;"></div>
    </div>
    <div style="text-align: center; width: 45%;">
        <div class="label-signature" style="font-size: 10px; color: #999; margin-bottom: 30px;">Signature du Parent</div>
        <div style="border-top: 1px solid #ccc;"></div>
    </div>
</div>

    </div>
</div>

@if($i === 0)
<div style="border-top: 2px dashed #ccc; text-align: center; position: relative; margin: 10px 0;">
    <span style="background: #f0f2f5; padding: 0 10px; font-size: 10px; color: #999; position: relative; top: -8px;">✂ Découper ici</span>
</div>
@endif

@endfor

</div>

@endsection