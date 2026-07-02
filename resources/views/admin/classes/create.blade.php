@extends('layouts.app')

@section('title', 'Ajouter une classe')

@section('menu')
    <div class="menu-label">Principal</div>
    <a href="/admin/dashboard" class="menu-item"><span>📊</span> Tableau de bord</a>

    <div class="menu-label">Gestion</div>
    <a href="/admin/utilisateurs" class="menu-item"><span>👥</span> Utilisateurs</a>
    <a href="/admin/eleves" class="menu-item"><span>🎒</span> Élèves</a>
    <a href="/admin/classes" class="menu-item active"><span>🏫</span> Classes</a>

    <div class="menu-label">Scolarité</div>
    <a href="/admin/paiements" class="menu-item"><span>💰</span> Paiements</a>
    <a href="/admin/notes" class="menu-item"><span>📝</span> Notes</a>
    <a href="/admin/absences" class="menu-item"><span>📅</span> Absences</a>

    <div class="menu-label">Secrétariat</div>
    <a href="/admin/plaintes" class="menu-item"><span>📋</span> Plaintes</a>
    <a href="/admin/recus" class="menu-item"><span>🧾</span> Reçus</a>
@endsection

@section('page_title', 'Ajouter une classe')

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

            <form method="POST" action="/admin/classes">
                @csrf

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Nom de la classe</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" placeholder="ex: Maternelle A" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Niveau</label>
                    <select name="niveau" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                        <option value="">-- Choisir un niveau --</option>
                        <option value="Maternelle 1" {{ old('niveau') === 'Maternelle 1' ? 'selected' : '' }}>Maternelle 1</option>
                        <option value="Maternelle 2" {{ old('niveau') === 'Maternelle 2' ? 'selected' : '' }}>Maternelle 2</option>
                        <option value="Maternelle 3" {{ old('niveau') === 'Maternelle 3' ? 'selected' : '' }}>Maternelle 3</option>
                        <option value="CP" {{ old('niveau') === 'CP' ? 'selected' : '' }}>CP</option>
                        <option value="CE1" {{ old('niveau') === 'CE1' ? 'selected' : '' }}>CE1</option>
                        <option value="CE2" {{ old('niveau') === 'CE2' ? 'selected' : '' }}>CE2</option>
                        <option value="CM1" {{ old('niveau') === 'CM1' ? 'selected' : '' }}>CM1</option>
                        <option value="CM2" {{ old('niveau') === 'CM2' ? 'selected' : '' }}>CM2</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Année scolaire</label>
                    <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', '2025-2026') }}" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Capacité maximale</label>
                    <input type="number" name="capacite" value="{{ old('capacite', 30) }}" min="1" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit"
                        style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
                        ✅ Enregistrer
                    </button>
                    <a href="/admin/classes"
                        style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
                        Annuler
                    </a>
                </div>

            </form>
        </div>
    </div>

@endsection