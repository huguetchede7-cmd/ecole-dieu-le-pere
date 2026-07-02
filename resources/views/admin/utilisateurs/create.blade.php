@extends('layouts.app')

@section('title', 'Ajouter un utilisateur')

@section('menu')
    <div class="menu-label">Principal</div>
    <a href="/admin/dashboard" class="menu-item"><span>📊</span> Tableau de bord</a>

    <div class="menu-label">Gestion</div>
    <a href="/admin/utilisateurs" class="menu-item active"><span>👥</span> Utilisateurs</a>
    <a href="/admin/eleves" class="menu-item"><span>🎒</span> Élèves</a>
    <a href="/admin/classes" class="menu-item"><span>🏫</span> Classes</a>

    <div class="menu-label">Scolarité</div>
    <a href="/admin/paiements" class="menu-item"><span>💰</span> Paiements</a>
    <a href="/admin/notes" class="menu-item"><span>📝</span> Notes</a>
    <a href="/admin/absences" class="menu-item"><span>📅</span> Absences</a>

    <div class="menu-label">Secrétariat</div>
    <a href="/admin/plaintes" class="menu-item"><span>📋</span> Plaintes</a>
    <a href="/admin/recus" class="menu-item"><span>🧾</span> Reçus</a>
@endsection

@section('page_title', 'Ajouter un utilisateur')

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

            <form method="POST" action="/admin/utilisateurs">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required
                            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required
                            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>

                </div>

                <div style="margin-top: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                </div>

                <div style="margin-top: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Mot de passe</label>
                    <input type="password" name="mot_de_passe" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                </div>

                <div style="margin-top: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Rôle</label>
                    <select name="role" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                        <option value="">-- Choisir un rôle --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrateur</option>
                        <option value="directeur" {{ old('role') === 'directeur' ? 'selected' : '' }}>Directeur</option>
                        <option value="enseignant" {{ old('role') === 'enseignant' ? 'selected' : '' }}>Enseignant</option>
                        <option value="comptable" {{ old('role') === 'comptable' ? 'selected' : '' }}>Comptable</option>
                        <option value="secretaire" {{ old('role') === 'secretaire' ? 'selected' : '' }}>Secrétaire</option>
                    </select>
                </div>

                <div style="margin-top: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Téléphone (optionnel)</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}"
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