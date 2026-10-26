@extends('layouts.app')

@section('title', $examen ? 'Modifier une session' : 'Nouvelle session')
@section('page_title', $examen ? 'Modifier une session' : 'Nouvelle session')

@section('content')

@php
    $v = fn($champ, $defaut = '') => old($champ, $examen ? ($examen->$champ instanceof \Carbon\Carbon ? $examen->$champ->format('Y-m-d') : $examen->$champ) : $defaut);
@endphp

<div style="max-width: 700px;">

@if($errors->any())
<div style="background: #fdecea; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
    @foreach($errors->all() as $error)
    <div>❌ {{ $error }}</div>
    @endforeach
</div>
@endif

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

<form method="POST" action="{{ $examen ? '/admin/examens/' . $examen->id : '/admin/examens' }}">
    @csrf
    @if($examen) @method('PUT') @endif

    <div>
        <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Titre de la session</label>
        <input type="text" name="titre" value="{{ $v('titre') }}" required placeholder="Ex : Composition du 1er trimestre"
            style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-top:20px;">
        <div>
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Type</label>
            <select name="type" required style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                @foreach(['Composition', 'Devoir', 'Examen blanc'] as $t)
                <option value="{{ $t }}" {{ $v('type', 'Composition') === $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Période</label>
            <select name="periode" required style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                <option value="1er trimestre" {{ $v('periode') === '1er trimestre' ? 'selected' : '' }}>1er trimestre</option>
                <option value="2eme trimestre" {{ $v('periode') === '2eme trimestre' ? 'selected' : '' }}>2ème trimestre</option>
                <option value="3eme trimestre" {{ $v('periode') === '3eme trimestre' ? 'selected' : '' }}>3ème trimestre</option>
            </select>
        </div>
        <div>
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Année scolaire</label>
            <input type="text" name="annee_scolaire" value="{{ $v('annee_scolaire', \App\Models\Parametre::anneeCourante()) }}" required
                style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
        </div>
    </div>

    <h3 style="font-size:14px; color:#1a73e8; margin:28px 0 12px;">📅 Calendrier</h3>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div>
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">1. Dépôt des épreuves, au plus tard le</label>
            <input type="date" name="date_limite_depot" value="{{ $v('date_limite_depot') }}" required
                style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
        </div>
        <div></div>
        <div>
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">2. Début de la composition</label>
            <input type="date" name="date_debut" value="{{ $v('date_debut') }}" required
                style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
        </div>
        <div>
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Fin de la composition</label>
            <input type="date" name="date_fin" value="{{ $v('date_fin') }}" required
                style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
        </div>
        <div>
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">3. Saisie des notes, au plus tard le</label>
            <input type="date" name="date_limite_notes" value="{{ $v('date_limite_notes') }}" required
                style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
        </div>
    </div>

    <div style="margin-top:20px;">
        <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Consignes aux enseignants (optionnel)</label>
        <textarea name="consignes" rows="3" placeholder="Ex : Déposez vos épreuves au format PDF ou Word, une par matière."
            style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none; font-family:inherit;">{{ $v('consignes') }}</textarea>
    </div>

    <div style="margin-top:30px; display:flex; gap:12px;">
        <button type="submit" style="background:#1a73e8; color:white; border:none; padding:12px 24px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">✅ Enregistrer</button>
        <a href="/admin/examens" style="background:#f0f0f0; color:#333; padding:12px 24px; border-radius:8px; font-size:14px; text-decoration:none;">Annuler</a>
    </div>
</form>

</div>
</div>

@endsection
