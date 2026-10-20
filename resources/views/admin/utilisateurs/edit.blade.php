@extends('layouts.app')

@section('title', 'Modifier un utilisateur')

@section('page_title', 'Modifier un utilisateur')

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

            <form method="POST" action="/admin/utilisateurs/{{ $utilisateur->id }}">
                @csrf
                @method('PUT')

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Civilité</label>
                    <select name="civilite"
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                        <option value="">-- Choisir --</option>
                        @foreach(['Monsieur', 'Madame', 'Mademoiselle'] as $c)
                            <option value="{{ $c }}" {{ old('civilite', $utilisateur->civilite) == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom', $utilisateur->nom) }}" required
                            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $utilisateur->prenom) }}" required
                            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Email (non modifiable)</label>
                    <input type="text" value="{{ $utilisateur->email }}" disabled
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; background: #f1f3f4; color: #777;">
                </div>

                <div style="margin-top: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Rôle (non modifiable)</label>
                    <input type="text" value="{{ ucfirst($utilisateur->role) }}" disabled
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; background: #f1f3f4; color: #777;">
                </div>

                @if($utilisateur->role === 'enseignant')
                <div style="margin-top: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Classe de l'enseignant</label>
                    <select name="classe_id" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                        <option value="">-- Choisir une classe --</option>
                        @foreach($classesDispo as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id', $classeActuelle->id ?? null) == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }} {{ $classe->niveau ? '(' . $classe->niveau . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div style="margin-top: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Téléphone (optionnel)</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $utilisateur->telephone) }}"
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                </div>

                <div style="margin-top: 30px; display: flex; gap: 12px;">
                    <button type="submit"
                        style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
                        ✅ Enregistrer
                    </button>
                    <a href="/admin/utilisateurs"
                        style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
                        Annuler
                    </a>
                </div>

            </form>
        </div>
    </div>

@endsection