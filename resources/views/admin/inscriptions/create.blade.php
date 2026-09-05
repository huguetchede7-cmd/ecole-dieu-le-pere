@extends('layouts.app')

@section('title', 'Ajouter une inscription')
@section('page_title', 'Ajouter une inscription')

@section('content')
<div style="max-width: 650px;">

@if($errors->any())
<div style="background: #fdecea; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
@foreach($errors->all() as $error)
<div>❌ {{ $error }}</div>
@endforeach
</div>
@endif

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

<div style="display:flex; gap:10px; margin-bottom:26px;">
    <button type="button" id="btn-mode-nouveau" onclick="setMode('nouveau')"
        style="flex:1; padding:12px; border-radius:8px; border:2px solid #1a73e8; background:#1a73e8; color:white; font-weight:600; font-size:14px; cursor:pointer;">
        🆕 Nouvel élève
    </button>
    <button type="button" id="btn-mode-reinscription" onclick="setMode('reinscription')"
        style="flex:1; padding:12px; border-radius:8px; border:2px solid #1a73e8; background:white; color:#1a73e8; font-weight:600; font-size:14px; cursor:pointer;">
        🔁 Réinscription
    </button>
</div>

<form method="POST" action="/admin/inscriptions" id="form-inscription">
@csrf

<input type="hidden" name="mode" id="mode" value="nouveau">
<input type="hidden" name="eleve_id" id="eleve_id" value="">

{{-- ===== BLOC REINSCRIPTION ===== --}}
<div id="bloc-reinscription" style="display:none; margin-bottom:24px;">
    <h5 style="margin-bottom: 16px; color: #333;">Rechercher l'élève</h5>

    <div style="display:flex; gap:10px; margin-bottom:16px;">
        <input type="text" id="matricule_recherche" placeholder="Ex: MAT-2026-00001"
            style="flex:1; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        <button type="button" onclick="rechercherEleve()"
            style="background:#1a73e8; color:white; border:none; padding:10px 20px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
            Rechercher
        </button>
    </div>

    <div id="resultat-recherche"></div>
</div>

{{-- ===== BLOC NOUVEL ELEVE ===== --}}
<div id="bloc-nouveau">
    <h5 style="margin-bottom: 16px; color: #333;">Élève et classe</h5>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Nom</label>
        <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Prénom</label>
        <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Date de naissance</label>
        <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance') }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Lieu de naissance</label>
    <input type="text" name="lieu_naissance" id="lieu_naissance" value="{{ old('lieu_naissance') }}"
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Sexe</label>
        <select name="sexe" id="sexe"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
            <option value="">-- Choisir --</option>
            <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Masculin</option>
            <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Féminin</option>
        </select>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Nom du parent</label>
        <input type="text" name="nom_parent" id="nom_parent" value="{{ old('nom_parent') }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Contact du parent</label>
        <input type="text" name="contact_parent" id="contact_parent" value="{{ old('contact_parent') }}"
            style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
    </div>
</div>

{{-- ===== CLASSE / ANNEE (toujours visible) ===== --}}
<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Classe / Groupe</label>
    <select name="classe_id" id="classe_id" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        <option value="">-- Choisir une classe --</option>
        @foreach($classes as $classe)
        <option value="{{ $classe->id }}" data-niveau="{{ $classe->niveau }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
            {{ $classe->nom }} {{ $classe->niveau ? '(' . $classe->niveau . ')' : '' }}
        </option>
        @endforeach
    </select>
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Année scolaire</label>
    <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', '2025-2026') }}" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Date d'inscription</label>
    <input type="date" name="date_inscription" value="{{ old('date_inscription', date('Y-m-d')) }}" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="margin-bottom: 28px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Statut</label>
    <select name="statut" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        <option value="actif" {{ old('statut', 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
        <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
    </select>
</div>

<h5 style="margin-bottom: 8px; color: #333;">Paiement à l'inscription</h5>
<p style="font-size: 13px; color: #999; margin-bottom: 16px;">
    Optionnel. Le parent peut payer les frais d'inscription, une tranche de scolarité, la totalité, ou ne rien payer maintenant.
</p>

<div id="paiements-container"></div>

<button type="button" id="add-paiement"
    style="background: #e8f0fe; color: #1a73e8; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; margin-bottom: 28px;">
    + Ajouter un paiement
</button>

<div style="display: flex; gap: 12px;">
    <button type="submit"
        style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
        ✅ Enregistrer l'inscription
    </button>
    <a href="/admin/inscriptions"
        style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
        Annuler
    </a>
</div>

</form>
</div>
</div>

<template id="paiement-row-template">
<div class="paiement-row" style="display: flex; gap: 10px; align-items: flex-end; margin-bottom: 14px; padding: 14px; background: #f8f9fa; border-radius: 8px;">
    <div style="flex: 2;">
        <label style="display: block; font-size: 12px; font-weight: 600; color: #333; margin-bottom: 4px;">Type de frais</label>
        <select name="paiements[__INDEX__][type_frais_id]" style="width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px;">
            <option value="">-- Choisir --</option>
            @foreach($typesFrais as $type)
            <option value="{{ $type->id }}">{{ $type->libelle }} ({{ number_format($type->montant, 0, ',', ' ') }} FCFA)</option>
            @endforeach
        </select>
    </div>
    <div style="flex: 1;">
        <label style="display: block; font-size: 12px; font-weight: 600; color: #333; margin-bottom: 4px;">Montant payé</label>
        <input type="number" step="1" min="0" name="paiements[__INDEX__][montant_paye]" placeholder="FCFA"
            style="width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px;">
    </div>
    <div style="flex: 1;">
        <label style="display: block; font-size: 12px; font-weight: 600; color: #333; margin-bottom: 4px;">Mode</label>
        <select name="paiements[__INDEX__][mode_paiement]" style="width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px;">
            <option value="Espèces">Espèces</option>
            <option value="Mobile Money">Mobile Money</option>
            <option value="Chèque">Chèque</option>
            <option value="Virement">Virement</option>
        </select>
    </div>
    <button type="button" class="remove-paiement" style="background: #fdecea; color: #c62828; border: none; padding: 8px 12px; border-radius: 6px; font-size: 13px; cursor: pointer;">
        ✕
    </button>
</div>
</template>

<script>
// ===== Gestion des paiements (inchangé) =====
let paiementIndex = 0;
const container = document.getElementById('paiements-container');
const template = document.getElementById('paiement-row-template');

document.getElementById('add-paiement').addEventListener('click', function () {
    const clone = template.content.cloneNode(true);
    clone.querySelectorAll('[name*="__INDEX__"]').forEach(el => {
        el.name = el.name.replace('__INDEX__', paiementIndex);
    });
    paiementIndex++;
    container.appendChild(clone);
});

container.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-paiement')) {
        e.target.closest('.paiement-row').remove();
    }
});

// ===== Ordre des niveaux (doit correspondre au contrôleur ClasseController) =====
const ordreNiveaux = ['Maternelle 1', 'Maternelle 2', 'CI', 'CP', 'CE1', 'CE2', 'CM1', 'CM2'];

// ===== Bascule Nouveau / Réinscription =====
function setMode(mode) {
    document.getElementById('mode').value = mode;

    const btnNouveau = document.getElementById('btn-mode-nouveau');
    const btnReinscription = document.getElementById('btn-mode-reinscription');
    const blocNouveau = document.getElementById('bloc-nouveau');
    const blocReinscription = document.getElementById('bloc-reinscription');

    if (mode === 'nouveau') {
        blocNouveau.style.display = 'block';
        blocReinscription.style.display = 'none';
        btnNouveau.style.background = '#1a73e8';
        btnNouveau.style.color = 'white';
        btnReinscription.style.background = 'white';
        btnReinscription.style.color = '#1a73e8';

        document.getElementById('nom').required = true;
        document.getElementById('prenom').required = true;
        document.getElementById('date_naissance').required = true;
        document.getElementById('sexe').required = true;
        document.getElementById('eleve_id').value = '';
    } else {
        blocNouveau.style.display = 'none';
        blocReinscription.style.display = 'block';
        btnReinscription.style.background = '#1a73e8';
        btnReinscription.style.color = 'white';
        btnNouveau.style.background = 'white';
        btnNouveau.style.color = '#1a73e8';

        document.getElementById('nom').required = false;
        document.getElementById('prenom').required = false;
        document.getElementById('date_naissance').required = false;
        document.getElementById('sexe').required = false;
    }
}

// ===== Recherche d'élève par matricule =====
function rechercherEleve() {
    const matricule = document.getElementById('matricule_recherche').value.trim();
    const resultatDiv = document.getElementById('resultat-recherche');

    if (!matricule) {
        resultatDiv.innerHTML = '<p style="color:#c62828; font-size:13px;">Tape un matricule.</p>';
        return;
    }

    resultatDiv.innerHTML = '<p style="color:#999; font-size:13px;">Recherche en cours...</p>';

    fetch(`/admin/inscriptions/rechercher-eleve/${encodeURIComponent(matricule)}`)
        .then(res => res.json())
        .then(data => {
            if (!data.trouve) {
                resultatDiv.innerHTML = '<p style="color:#c62828; font-size:13px;">❌ Aucun élève trouvé avec ce matricule.</p>';
                document.getElementById('eleve_id').value = '';
                return;
            }

            document.getElementById('eleve_id').value = data.eleve.id;

            let html = `
                <div style="background:#e8f0fe; border-radius:8px; padding:16px; margin-bottom:16px;">
                    <p style="font-size:14px; font-weight:600; color:#333; margin-bottom:4px;">
                        ✅ ${data.eleve.nom} ${data.eleve.prenom} (${data.eleve.matricule})
                    </p>`;

            if (data.derniere_inscription) {
                const d = data.derniere_inscription;
                let decisionTexte = '⏳ Décision non encore renseignée';
                let decisionCouleur = '#999';

                if (d.decision === 'admis') {
                    decisionTexte = '✅ Admis(e) en ' + d.annee_scolaire;
                    decisionCouleur = '#2e7d32';
                } else if (d.decision === 'refuse') {
                    decisionTexte = '❌ Refusé(e) en ' + d.annee_scolaire;
                    decisionCouleur = '#c62828';
                }

                html += `
                    <p style="font-size:13px; color:#555; margin-bottom:4px;">
                        Dernière classe : <strong>${d.classe ?? 'N/A'}</strong> (${d.niveau ?? 'N/A'}) — ${d.annee_scolaire}
                    </p>
                    <p style="font-size:13px; color:${decisionCouleur}; font-weight:600;">
                        ${decisionTexte}
                    </p>`;

                // Filtrer les classes proposées selon le niveau actuel et le niveau suivant
                const indexActuel = ordreNiveaux.indexOf(d.niveau);
                const niveauSuivant = (indexActuel >= 0 && indexActuel < ordreNiveaux.length - 1)
                    ? ordreNiveaux[indexActuel + 1]
                    : null;

                filtrerClasses(d.niveau, niveauSuivant);
            } else {
                html += `<p style="font-size:13px; color:#999;">Aucune inscription précédente trouvée pour cet élève.</p>`;
                filtrerClasses(null, null);
            }

            html += `</div>`;
            resultatDiv.innerHTML = html;
        })
        .catch(() => {
            resultatDiv.innerHTML = '<p style="color:#c62828; font-size:13px;">Erreur lors de la recherche.</p>';
        });
}

// Ne montrer dans le menu "Classe" que le niveau actuel + le niveau supérieur
function filtrerClasses(niveauActuel, niveauSuivant) {
    const select = document.getElementById('classe_id');
    const options = select.querySelectorAll('option');

    options.forEach(opt => {
        if (!opt.value) {
            opt.style.display = 'block';
            return;
        }
        const niveau = opt.getAttribute('data-niveau');
        if (niveau === niveauActuel || niveau === niveauSuivant) {
            opt.style.display = 'block';
        } else {
            opt.style.display = 'none';
        }
    });

    select.value = '';
}
</script>

@endsection