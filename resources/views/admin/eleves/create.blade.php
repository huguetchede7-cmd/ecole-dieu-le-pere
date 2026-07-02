@extends('layouts.app')

@section('title', 'Ajouter un élève')

@section('menu')
    <div class="menu-label">Principal</div>
    <a href="/admin/dashboard" class="menu-item"><span>📊</span> Tableau de bord</a>

    <div class="menu-label">Gestion</div>
    <a href="/admin/utilisateurs" class="menu-item"><span>👥</span> Utilisateurs</a>
    <a href="/admin/eleves" class="menu-item active"><span>🎒</span> Élèves</a>
    <a href="/admin/classes" class="menu-item"><span>🏫</span> Classes</a>

    <div class="menu-label">Scolarité</div>
    <a href="/admin/paiements" class="menu-item"><span>💰</span> Paiements</a>
    <a href="/admin/notes" class="menu-item"><span>📝</span> Notes</a>
    <a href="/admin/absences" class="menu-item"><span>📅</span> Absences</a>

    <div class="menu-label">Secrétariat</div>
    <a href="/admin/plaintes" class="menu-item"><span>📋</span> Plaintes</a>
    <a href="/admin/recus" class="menu-item"><span>🧾</span> Reçus</a>
@endsection

@section('page_title', 'Ajouter un élève')

@section('content')

<div style="max-width:750px;">

    @if($errors->any())
        <div style="background:#fdecea; color:#c62828; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
            @foreach($errors->all() as $error)
                <div>❌ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="/admin/eleves" enctype="multipart/form-data">
        @csrf

        {{-- INFOS ENFANT --}}
        <div style="background:white; border-radius:12px; padding:28px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:20px;">
            <h3 style="font-size:15px; color:#1a73e8; margin-bottom:20px; padding-bottom:10px; border-bottom:1px solid #f0f0f0;">
                👦 Informations de l'enfant
            </h3>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Nom *</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required
                        style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Prénom *</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" required
                        style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Date de naissance *</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" required
                        style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Sexe *</label>
                    <select name="sexe" required
                        style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
                        <option value="">-- Sélectionner --</option>
                        <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Garçon</option>
                        <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Fille</option>
                    </select>
                </div>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Photo de l'élève</label>
                <input type="file" name="photo" accept="image/*"
                    style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none; background:white;">
            </div>
        </div>

        {{-- INSCRIPTION --}}
        <div style="background:white; border-radius:12px; padding:28px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:20px;">
            <h3 style="font-size:15px; color:#1a73e8; margin-bottom:20px; padding-bottom:10px; border-bottom:1px solid #f0f0f0;">
                🏫 Inscription
            </h3>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Classe *</label>
                    <select name="classe_id" required
                        style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
                        <option value="">-- Sélectionner une classe --</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }} ({{ $classe->niveau }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Année scolaire *</label>
                    <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', '2025-2026') }}" required
                        style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
                </div>
            </div>
        </div>

        {{-- INFOS PARENT --}}
        <div style="background:white; border-radius:12px; padding:28px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:24px;">
            <h3 style="font-size:15px; color:#1a73e8; margin-bottom:20px; padding-bottom:10px; border-bottom:1px solid #f0f0f0;">
                👨‍👩‍👦 Informations du Parent / Tuteur
            </h3>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Nom du Parent *</label>
                    <input type="text" name="nom_parent" value="{{ old('nom_parent') }}" required
                        style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Contact / Téléphone *</label>
                    <input type="text" name="contact_parent" value="{{ old('contact_parent') }}" required
                        style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none;">
                </div>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Adresse</label>
                <textarea name="adresse" rows="3"
                    style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; outline:none; resize:vertical;">{{ old('adresse') }}</textarea>
            </div>
        </div>

        {{-- BOUTONS --}}
        <div style="display:flex; gap:12px;">
            <button type="submit"
                style="background:#1a73e8; color:white; border:none; padding:12px 28px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                ✅ Enregistrer l'élève
            </button>
            <a href="/admin/eleves"
                style="background:#f0f0f0; color:#333; padding:12px 28px; border-radius:8px; font-size:14px; text-decoration:none; font-weight:600;">
                Annuler
            </a>
        </div>

    </form>
</div>

@endsection