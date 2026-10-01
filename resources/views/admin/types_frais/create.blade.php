@extends('layouts.app')

@section('title', 'Ajouter un type de frais')
@section('page_title', 'Ajouter un type de frais')

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

<form method="POST" action="/admin/types-frais">
@csrf

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Libellé</label>
    <input type="text" name="libelle" value="{{ old('libelle') }}" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Catégorie</label>
   <select name="categorie" required
    style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    <option value="inscription" {{ old('categorie') === 'inscription' ? 'selected' : '' }}>Inscription (nouvel élève)</option>
    <option value="reinscription" {{ old('categorie') === 'reinscription' ? 'selected' : '' }}>Réinscription</option>
    <option value="scolarite" {{ old('categorie') === 'scolarite' ? 'selected' : '' }}>Scolarité (obligatoire, tous les élèves actifs)</option>
    <option value="autre" {{ old('categorie', 'autre') === 'autre' ? 'selected' : '' }}>Autre (cantine, examen, cotisation...)</option>
</select>
</div>

<div style="margin-bottom: 20px;">
    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #333;">
        <input type="checkbox" name="varie_par_niveau" id="varie_par_niveau" value="1" onchange="toggleMontant()" {{ old('varie_par_niveau') ? 'checked' : '' }}>
        Ce frais varie selon le niveau
    </label>
</div>

<div id="bloc-montant-unique" style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Montant (FCFA)</label>
    <input type="number" step="1" min="0" name="montant" id="montant" value="{{ old('montant') }}"
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div id="bloc-montants-niveaux" style="display:none; margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 10px;">Montant par niveau (FCFA)</label>

    @foreach($niveaux as $niveau)
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
        <span style="width: 130px; font-size: 13px; color: #333;">{{ $niveau }}</span>
        <input type="number" step="1" min="0" name="montants_niveaux[{{ $niveau }}]" placeholder="Optionnel"
            style="flex:1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; outline: none;">
    </div>
    @endforeach
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Description (optionnel)</label>
    <input type="text" name="description" value="{{ old('description') }}"
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="display: flex; gap: 12px;">
    <button type="submit"
        style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
        ✅ Enregistrer
    </button>
    <a href="/admin/types-frais"
        style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
        Annuler
    </a>
</div>

</form>
</div>
</div>

<script>
function toggleMontant() {
    const varie = document.getElementById('varie_par_niveau').checked;
    document.getElementById('bloc-montant-unique').style.display = varie ? 'none' : 'block';
    document.getElementById('bloc-montants-niveaux').style.display = varie ? 'block' : 'none';
}
toggleMontant();
</script>

@endsection