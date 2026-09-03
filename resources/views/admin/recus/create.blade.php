@extends('layouts.app')

@section('title', 'Générer un reçu')
@section('page_title', 'Générer un reçu')

@section('content')
<div style="max-width: 600px;">

@if($errors->any())
<div style="background: #fdecea; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
    @foreach($errors->all() as $error)
    <div>❌ {{ $error }}</div>
    @endforeach
</div>
@endif

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

<form method="POST" action="/admin/recus">
    @csrf

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Inscription</label>
        <select name="inscription_id" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            <option value="">-- Choisir une inscription --</option>
            @foreach($inscriptions as $inscription)
            <option value="{{ $inscription->id }}" {{ old('inscription_id') == $inscription->id ? 'selected' : '' }}>
                {{ $inscription->eleve->nom ?? '' }} {{ $inscription->eleve->prenom ?? '' }} — {{ $inscription->annee_scolaire }}
            </option>
            @endforeach
        </select>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Paiements à rattacher (optionnel)</label>
        @forelse($paiementsLibres as $paiement)
            <label style="display: block; font-size: 14px; color: #333; margin-bottom: 8px;">
                <input type="checkbox" name="paiement_ids[]" value="{{ $paiement->id }}">
                {{ $paiement->eleve->nom ?? '' }} {{ $paiement->eleve->prenom ?? '' }} —
                {{ $paiement->typeFrais->libelle ?? '' }} —
                {{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA ({{ $paiement->date_paiement }})
            </label>
        @empty
            <p style="font-size: 13px; color: #999;">Aucun paiement en attente de reçu.</p>
        @endforelse
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Date d'émission</label>
        <input type="date" name="date_emission" value="{{ old('date_emission', date('Y-m-d')) }}" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="display: flex; gap: 12px;">
        <button type="submit"
            style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
            ✅ Générer le reçu
        </button>
        <a href="/admin/recus"
            style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
            Annuler
        </a>
    </div>

</form>
</div>
</div>
@endsection