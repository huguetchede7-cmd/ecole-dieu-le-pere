@extends('layouts.app')

@section('title', 'Corriger une note')
@section('page_title', 'Corriger une note')

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

<p style="font-size:14px; color:#555; margin-bottom:20px; line-height:1.8;">
    <strong>{{ $note->eleve->nom ?? '' }} {{ $note->eleve->prenom ?? '' }}</strong><br>
    {{ $note->matiere->nom ?? '' }} · {{ $note->periode }} · {{ $note->annee_scolaire }}
</p>

<form method="POST" action="/admin/notes/{{ $note->id }}">
    @csrf
    @method('PUT')

    <div style="display: flex; gap: 16px; margin-bottom: 20px;">
        <div style="flex: 1;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Note obtenue</label>
            <input type="number" name="note" value="{{ old('note', (float) $note->note) }}" min="0" step="0.01" required
                style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        </div>
        <div style="flex: 1;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Note maximale</label>
            <input type="number" name="note_max" value="{{ old('note_max', (float) $note->note_max) }}" min="1" step="0.01" required
                style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Motif de la correction</label>
        <input type="text" name="motif" value="{{ old('motif') }}" placeholder="Ex : erreur de saisie, copie revue…"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        <p style="font-size:12px; color:#888; margin-top:6px;">Ce motif sera visible par l'enseignant qui a saisi la note.</p>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Observation (optionnel)</label>
        <input type="text" name="observation" value="{{ old('observation', $note->observation) }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="display: flex; gap: 12px;">
        <button type="submit" style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">✅ Enregistrer la correction</button>
                <a href="{{ route('admin.notes.eleve', ['eleve' => $note->eleve_id, 'annee_scolaire' => $note->annee_scolaire]) }}" style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">Annuler</a>
</form>

</div>
</div>

@endsection