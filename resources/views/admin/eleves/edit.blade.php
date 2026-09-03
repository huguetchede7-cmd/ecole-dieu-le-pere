@extends('layouts.app')

@section('title', 'Modifier un Élève')
@section('page_title', 'Modifier un Élève')

@section('content')
<div style="max-width: 700px;">

    <a href="{{ route('admin.eleves.index') }}"
       style="display: inline-block; margin-bottom: 20px; color: #1a73e8; text-decoration: none; font-size: 14px;">
        ← Retour à la liste
    </a>

    @if($errors->any())
        <div style="background: #fdecea; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            @foreach($errors->all() as $error)
                <div>❌ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

        <form action="{{ route('admin.eleves.update', $eleve->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h5 style="margin-bottom: 16px; color: #333;">Informations de l'enfant</h5>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Nom *</label>
                <input type="text" name="nom" value="{{ old('nom', $eleve->nom) }}" required
                    style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Prénom *</label>
                <input type="text" name="prenom" value="{{ old('prenom', $eleve->prenom) }}" required
                    style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Date de Naissance *</label>
                <input type="date" name="date_naissance" value="{{ old('date_naissance', \Carbon\Carbon::parse($eleve->date_naissance)->format('Y-m-d')) }}" required
                    style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Sexe *</label>
                <select name="sexe" required
                    style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                    <option value="M" {{ old('sexe', $eleve->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                    <option value="F" {{ old('sexe', $eleve->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
                </select>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Photo de l'élève</label>
                @if($eleve->photo)
                    <div style="margin-bottom: 10px;">
                        <img src="{{ asset('storage/' . $eleve->photo) }}" alt="Photo actuelle"
                            style="max-width: 140px; border-radius: 8px; border: 1px solid #eee;">
                        <div style="font-size: 12px; color: #999; margin-top: 4px;">Photo actuelle</div>
                    </div>
                @endif
                <input type="file" name="photo" accept="image/*"
                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                <small style="color: #999; font-size: 12px;">Laissez vide si vous ne souhaitez pas changer la photo</small>
            </div>

            <h5 style="margin-bottom: 16px; color: #333;">Inscription</h5>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Classe *</label>
                <select name="classe_id" required
                    style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                    <option value="">Sélectionner une classe</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}"
                        {{ old('classe_id', $currentInscription->classe_id ?? null) == $classe->id ? 'selected' : '' }}>
                        {{ $classe->nom }} {{ $classe->niveau ? '(' . $classe->niveau . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Année Scolaire *</label>
                <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $currentInscription->annee_scolaire ?? '') }}" placeholder="ex: 2025-2026" required
                    style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            </div>

            <h5 style="margin-bottom: 16px; color: #333;">Informations du parent</h5>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Nom du parent *</label>
                <input type="text" name="nom_parent" value="{{ old('nom_parent', $eleve->nom_parent) }}" required
                    style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Contact parent *</label>
                <input type="text" name="contact_parent" value="{{ old('contact_parent', $eleve->contact_parent) }}" required
                    style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Adresse</label>
                <textarea name="adresse" rows="2"
                    style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">{{ old('adresse', $eleve->adresse) }}</textarea>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit"
                    style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
                    ✅ Mettre à jour
                </button>
                <a href="{{ route('admin.eleves.index') }}"
                    style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection