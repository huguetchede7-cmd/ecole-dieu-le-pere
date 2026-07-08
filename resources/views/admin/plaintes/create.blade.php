@extends('layouts.app')

@section('title', 'Déposer une plainte')

@section('page_title', 'Déposer une plainte')

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

<form method="POST" action="/admin/plaintes">
    @csrf

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Élève concerné</label>
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
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Description de la plainte</label>
        <textarea name="description" rows="5" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; font-family: inherit; resize: vertical;">{{ old('description') }}</textarea>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Date de la plainte</label>
        <input type="date" name="date_plainte" value="{{ old('date_plainte', date('Y-m-d')) }}" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="display: flex; gap: 12px;">
        <button type="submit"
            style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
            ✅ Enregistrer
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
