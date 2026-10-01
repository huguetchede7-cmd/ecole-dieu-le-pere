@extends('layouts.app')

@section('title', 'Ajouter une échéance')
@section('page_title', 'Ajouter une échéance')

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

<form method="POST" action="/admin/echeances">
@csrf

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Libellé</label>
    <input type="text" name="libelle" value="{{ old('libelle') }}" placeholder="Ex: 1ère tranche scolarité" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Année scolaire</label>
    <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', '2025-2026') }}" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Date limite</label>
    <input type="date" name="date_limite" value="{{ old('date_limite') }}" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Montant (FCFA)</label>
    <input type="number" step="1" min="0" name="montant" value="{{ old('montant') }}" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="display: flex; gap: 12px;">
    <button type="submit" style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
        ✅ Enregistrer
    </button>
    <a href="/admin/echeances" style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
        Annuler
    </a>
</div>

</form>
</div>
</div>

@endsection