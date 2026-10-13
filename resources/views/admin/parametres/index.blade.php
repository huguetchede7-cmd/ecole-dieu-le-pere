@extends('layouts.app')

@section('title', 'Paramètres')
@section('page_title', 'Paramètres')

@section('content')

<div style="max-width: 760px;">

@if(session('success'))
<div style="background: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
    ✅ {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background: #fdecea; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
@foreach($errors->all() as $error)
<div>❌ {{ $error }}</div>
@endforeach
</div>
@endif

<form method="POST" action="{{ url('/admin/parametres') }}">
@csrf
@method('PUT')

<div style="background: white; border-radius: 12px; padding: 28px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px;">
    <h3 style="margin: 0 0 4px; font-size: 16px;">🏫 Informations de l'école</h3>
    <p style="margin: 0 0 20px; font-size: 13px; color: #666;">Ces informations apparaissent sur les reçus et les bulletins.</p>

    <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Nom de l'école</label>
        <input type="text" name="ecole_nom" value="{{ old('ecole_nom', $ecole['nom']) }}" required
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
    </div>

    <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Ministère de tutelle</label>
        <input type="text" name="ecole_ministere" value="{{ old('ecole_ministere', $ecole['ministere']) }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
    </div>

    <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">N° d'autorisation</label>
        <input type="text" name="ecole_autorisation" value="{{ old('ecole_autorisation', $ecole['autorisation']) }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
    </div>

    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 180px; margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Téléphone</label>
            <input type="text" name="ecole_telephone" value="{{ old('ecole_telephone', $ecole['telephone']) }}"
                style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
        </div>
        <div style="flex: 1; min-width: 180px; margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Boîte postale / ville</label>
            <input type="text" name="ecole_bp" value="{{ old('ecole_bp', $ecole['bp']) }}"
                style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
        </div>
    </div>

    <div>
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Email</label>
        <input type="email" name="ecole_email" value="{{ old('ecole_email', $ecole['email']) }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
    </div>
</div>

<div style="background: white; border-radius: 12px; padding: 28px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px;">
    <h3 style="margin: 0 0 4px; font-size: 16px;">📅 Année scolaire en cours</h3>
    <p style="margin: 0 0 20px; font-size: 13px; color: #666;">Elle est proposée par défaut sur le tableau de bord et le livret de caisse.</p>

    <select name="annee_scolaire_courante" required
        style="padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; min-width: 200px;">
        @foreach($anneesDisponibles as $a)
        <option value="{{ $a }}" {{ old('annee_scolaire_courante', $anneeCourante) === $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
    </select>
</div>

<button type="submit"
    style="padding: 12px 28px; border: none; background: #e8a317; color: white; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; margin-bottom: 28px;">
    Enregistrer les paramètres
</button>

</form>

<div style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 12px;">Autres réglages</div>
<div style="display: flex; gap: 14px; flex-wrap: wrap;">
    <a href="{{ url('/admin/types-frais') }}" style="flex: 1; min-width: 200px; background: white; border-radius: 12px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; color: #333;">
        <div style="font-size: 20px;">💵</div>
        <div style="font-weight: 600; margin-top: 6px;">Types de frais</div>
        <div style="font-size: 12px; color: #888;">Montants par niveau et catégories</div>
    </a>
    <a href="{{ url('/admin/echeances') }}" style="flex: 1; min-width: 200px; background: white; border-radius: 12px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; color: #333;">
        <div style="font-size: 20px;">📅</div>
        <div style="font-weight: 600; margin-top: 6px;">Échéances</div>
        <div style="font-size: 12px; color: #888;">Dates limites de paiement</div>
    </a>
    <a href="{{ url('/admin/utilisateurs') }}" style="flex: 1; min-width: 200px; background: white; border-radius: 12px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none; color: #333;">
        <div style="font-size: 20px;">👥</div>
        <div style="font-weight: 600; margin-top: 6px;">Utilisateurs</div>
        <div style="font-size: 12px; color: #888;">Comptes et rôles</div>
    </a>
</div>

</div>

@endsection
