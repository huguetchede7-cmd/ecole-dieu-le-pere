@extends('layouts.app')

@section('title', 'Modifier une classe')

@section('page_title', 'Modifier une classe')

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

            <form method="POST" action="/admin/classes/{{ $classe->id }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Nom de la classe</label>
                    <input type="text" name="nom" value="{{ old('nom', $classe->nom) }}" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Niveau</label>
                    <select name="niveau" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                        <option value="">-- Choisir un niveau --</option>
                        @foreach(['Maternelle 1','Maternelle 2','Maternelle 3','CP','CE1','CE2','CM1','CM2'] as $n)
                            <option value="{{ $n }}" {{ old('niveau', $classe->niveau) === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Année scolaire</label>
                    <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', $classe->annee_scolaire) }}" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Capacité maximale</label>
                    <input type="number" name="capacite" value="{{ old('capacite', $classe->capacite) }}" min="1" required
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