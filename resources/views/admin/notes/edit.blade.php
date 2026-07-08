@extends('layouts.app')

@section('title', 'Modifier une note')

@section('page_title', 'Modifier une note')

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

<form method="POST" action="/admin/notes/{{ $note->id }}">
    @csrf
    @method('PUT')

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Élève</label>
        <select name="eleve_id" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            @foreach($eleves as $eleve)
            <option value="{{ $eleve->id }}" {{ old('eleve_id', $note->eleve_id) == $eleve->id ? 'selected' : '' }}>
                {{ $eleve->nom }} {{ $eleve->prenom }}
            </option>
            @endforeach
        </select>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Matière</label>
        <select name="matiere_id" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            @foreach($matieres as $matiere)
            <option value="{{ $matiere->id }}" {{ old('matiere_id', $note->matiere_id) == $matiere->id ? 'selected' : '' }}>
                {{ $matiere->nom }}
            </option>
            @endforeach
        </select>
    </div>

    <div style="display: flex; gap: 16px; margin-bottom: 20px;">
        <div style="flex: 1;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Note obtenue</label>
            <input type="number" name="note" value="{{ old('note', $note->note) }}" min="0" step="0.01" required
                style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        </div>
        <div style="flex: 1;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Note maximale</label>
            <input type="number" name="note_max" value="{{ old('note_max', $note->note_max) }}" min="1" step="0.01" required
                style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Période</label>
        <select name="periode" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            <option value="1er trimestre" {{ old('periode', $note->periode) === '1er trimestre' ? 'selected' : '' }}>1er trimestre</option>
            <option value="2eme trimestre" {{ old('periode', $note->periode) === '2eme trimestre' ? 'selected' : '' }}>2ème trimestre</option>
            <option value="3eme trimestre" {{ old('periode', $note->periode) === '3eme trimestre' ? 'selected' : '' }}>3ème trimestre</option>
        </select>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Année scolaire</label>
        <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $note->annee_scolaire) }}" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Observation (optionnel)</label>
        <input type="text" name="observation" value="{{ old('observation', $note->observation) }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="display: flex; gap: 12px;">
        <button type="submit"
            style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
            ✅ Mettre à jour
        </button>
        <a href="/admin/notes"
            style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
            Annuler
        </a>
    </div>

</form>
</div>
</div>

@endsection
