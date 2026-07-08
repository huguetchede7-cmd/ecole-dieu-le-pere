@extends('layouts.app')

@section('title', 'Enregistrer un paiement')

@section('page_title', 'Enregistrer un paiement')

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

<form method="POST" action="/admin/paiements">
    @csrf

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Élève</label>
        <select name="eleve_id" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            <option value="">-- Choisir un élève --</option>
            @foreach($eleves as $eleve)
            <option value="{{ $eleve->id }}" {{ old('eleve_id') == $eleve->id ? 'selected' : '' }}>
                {{ $eleve->nom }} {{ $eleve->prenom }}
            </option>
            @endforeach
        </select>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Type de frais</label>
        <select name="type_frais_id" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            <option value="">-- Choisir un type de frais --</option>
            @foreach($typesFrais as $type)
            <option value="{{ $type->id }}" {{ old('type_frais_id') == $type->id ? 'selected' : '' }}>
                {{ $type->libelle }} ({{ number_format($type->montant, 0, ',', ' ') }} FCFA)
            </option>
            @endforeach
        </select>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Montant payé (FCFA)</label>
        <input type="number" name="montant_paye" value="{{ old('montant_paye') }}" min="0" step="0.01" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Date du paiement</label>
        <input type="date" name="date_paiement" value="{{ old('date_paiement', date('Y-m-d')) }}" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Mode de paiement</label>
        <select name="mode_paiement" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            <option value="especes" {{ old('mode_paiement') === 'especes' ? 'selected' : '' }}>Espèces</option>
            <option value="mobile_money" {{ old('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
        </select>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Observation (optionnel)</label>
        <input type="text" name="observation" value="{{ old('observation') }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="display: flex; gap: 12px;">
        <button type="submit"
            style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
            ✅ Enregistrer
        </button>
        <a href="/admin/paiements"
            style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
            Annuler
        </a>
    </div>

</form>
</div>
</div>

@endsection
