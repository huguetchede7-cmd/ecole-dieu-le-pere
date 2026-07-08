@extends('layouts.app')

@section('title', 'Traiter la plainte')

@section('page_title', 'Traiter la plainte')

@section('content')

<div style="max-width: 600px;">

@if($errors->any())
<div style="background: #fdecea; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
    @foreach($errors->all() as $error)
    <div>❌ {{ $error }}</div>
    @endforeach
</div>
@endif

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px;">
    <span style="font-size: 12px; color: #999;">Élève</span>
    <p style="font-size: 15px; color: #333; margin-bottom: 16px;">{{ $plainte->eleve->nom ?? 'N/A' }} {{ $plainte->eleve->prenom ?? '' }}</p>

    <span style="font-size: 12px; color: #999;">Description</span>
    <p style="font-size: 14px; color: #333; margin-bottom: 16px; line-height: 1.5;">{{ $plainte->description }}</p>

    <span style="font-size: 12px; color: #999;">Date de la plainte</span>
    <p style="font-size: 14px; color: #333;">{{ $plainte->date_plainte }}</p>
</div>

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

<form method="POST" action="/admin/plaintes/{{ $plainte->id }}">
    @csrf
    @method('PUT')

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Statut</label>
        <select name="statut" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            <option value="en_cours" {{ old('statut', $plainte->statut) === 'en_cours' ? 'selected' : '' }}>En cours</option>
            <option value="resolue" {{ old('statut', $plainte->statut) === 'resolue' ? 'selected' : '' }}>Résolue</option>
            <option value="rejetee" {{ old('statut', $plainte->statut) === 'rejetee' ? 'selected' : '' }}>Rejetée</option>
        </select>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Réponse / Note de traitement</label>
        <textarea name="reponse" rows="4"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; font-family: inherit; resize: vertical;">{{ old('reponse', $plainte->reponse) }}</textarea>
    </div>

    <div style="display: flex; gap: 12px;">
        <button type="submit"
            style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
            ✅ Mettre à jour
        </button>
        <a href="/admin/plaintes"
            style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
            Annuler
        </a>
    </div>

</form>
</div>
</div>

@endsection
