@extends('layouts.app')

@section('title', 'Enregistrer un paiement')
@section('page_title', 'Enregistrer un paiement')

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

<form method="POST" action="/admin/paiements" id="form-paiement">
@csrf

<input type="hidden" name="eleve_id" id="eleve_id" value="">

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Classe</label>
    <select name="classe_id" id="classe_id" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        <option value="">-- Choisir une classe --</option>
        @foreach($classes as $classe)
        <option value="{{ $classe->id }}" data-niveau="{{ $classe->niveau }}">
            {{ $classe->nom }} {{ $classe->niveau ? '(' . $classe->niveau . ')' : '' }}
        </option>
        @endforeach
    </select>
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Élève</label>

    <input type="text" id="recherche_eleve" placeholder="Rechercher par nom ou matricule..." disabled
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; margin-bottom: 10px;">

    <div id="liste-eleves" style="border: 1px solid #ddd; border-radius: 8px; max-height: 220px; overflow-y: auto;">
        <div style="padding:12px; font-size:13px; color:#999;">Choisis d'abord une classe.</div>
    </div>

    <div id="eleve-choisi" style="display:none; margin-top:10px; background:#e8f0fe; border-radius:8px; padding:10px 14px; font-size:14px; color:#333; font-weight:600;"></div>
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Type de frais</label>
    <select name="type_frais_id" id="type_frais_id" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        <option value="">-- Choisir une classe d'abord --</option>
    </select>
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Montant payé (FCFA)</label>
    <input type="number" name="montant_paye" value="{{ old('montant_paye') }}" min="0" step="0.01" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Date du paiement</label>
    <input type="date" name="date_paiement" value="{{ old('date_paiement', date('Y-m-d')) }}" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Mode de paiement</label>
    <select name="mode_paiement" required
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        <option value="especes" {{ old('mode_paiement') === 'especes' ? 'selected' : '' }}>Espèces</option>
        <option value="mobile_money" {{ old('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
    </select>
</div>

<div style="margin-bottom: 20px;">
    <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Observation (optionnel)</label>
    <input type="text" name="observation" value="{{ old('observation') }}"
        style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
</div>

<div style="display: flex; gap: 12px;">
    <button type="submit"
        style="background: #1a73e8; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
        ✅ Enregistrer
    </button>
    <a href="/admin/paiements"
        style="background: #f0f0f0; color: #333; padding: 12px 24px; border-radius: 8px; font-size: 14px; text-decoration: none;">
        Annuler
    </a>
</div>

</form>
</div>
</div>

<script>
const typesFrais = @json($typesFrais);
let elevesClasse = [];

document.getElementById('classe_id').addEventListener('change', function () {
    const classeId = this.value;
    const niveau = this.options[this.selectedIndex]?.getAttribute('data-niveau');
    const listeDiv = document.getElementById('liste-eleves');
    const rechercheInput = document.getElementById('recherche_eleve');
    const eleveChoisi = document.getElementById('eleve-choisi');
    const typeFraisSelect = document.getElementById('type_frais_id');

    document.getElementById('eleve_id').value = '';
    eleveChoisi.style.display = 'none';
    rechercheInput.value = '';
    rechercheInput.disabled = true;

    // Remplit le menu "Type de frais" avec le bon montant selon le niveau
    typeFraisSelect.innerHTML = '<option value="">-- Choisir --</option>';
    typesFrais.forEach(t => {
        let montant = t.varie_par_niveau ? (t.montants_par_niveau[niveau] ?? null) : t.montant;
        let texte = t.libelle + (montant !== null ? ` (${Number(montant).toLocaleString('fr-FR')} FCFA)` : ' (montant non défini)');
        const opt = document.createElement('option');
        opt.value = t.id;
        opt.textContent = texte;
        typeFraisSelect.appendChild(opt);
    });

    if (!classeId) {
        listeDiv.innerHTML = '<div style="padding:12px; font-size:13px; color:#999;">Choisis d\'abord une classe.</div>';
        return;
    }

    listeDiv.innerHTML = '<div style="padding:12px; font-size:13px; color:#999;">Chargement...</div>';

    fetch(`/admin/paiements/eleves-classe/${classeId}`)
        .then(res => res.json())
        .then(eleves => {
            elevesClasse = eleves;
            rechercheInput.disabled = false;
            afficherListeEleves(eleves);
        })
        .catch(() => {
            listeDiv.innerHTML = '<div style="padding:12px; font-size:13px; color:#c62828;">Erreur de chargement.</div>';
        });
});

function afficherListeEleves(eleves) {
    const listeDiv = document.getElementById('liste-eleves');

    if (eleves.length === 0) {
        listeDiv.innerHTML = '<div style="padding:12px; font-size:13px; color:#999;">Aucun élève inscrit dans cette classe.</div>';
        return;
    }

    let html = '';
    eleves.forEach(e => {
        html += `
            <div class="item-eleve" data-id="${e.id}" data-nom="${e.nom} ${e.prenom}" data-matricule="${e.matricule ?? ''}"
                style="padding:10px 14px; cursor:pointer; font-size:14px; color:#333; border-bottom:1px solid #f0f0f0;"
                onclick="choisirEleve(${e.id}, '${e.nom} ${e.prenom}', '${e.matricule ?? 'sans matricule'}')">
                ${e.nom} ${e.prenom} <span style="color:#999; font-size:12px;">(${e.matricule ?? 'sans matricule'})</span>
            </div>`;
    });
    listeDiv.innerHTML = html;
}

function choisirEleve(id, nomComplet, matricule) {
    document.getElementById('eleve_id').value = id;
    const eleveChoisi = document.getElementById('eleve-choisi');
    eleveChoisi.style.display = 'block';
    eleveChoisi.innerHTML = `✅ ${nomComplet} (${matricule})`;

    const classeId = document.getElementById('classe_id').value;

    fetch(`/admin/paiements/solde-eleve/${id}/${classeId}`)
        .then(res => res.json())
        .then(soldes => {
            const typeFraisSelect = document.getElementById('type_frais_id');

            Array.from(typeFraisSelect.options).forEach(opt => {
                if (!opt.value) return;

                const info = soldes.find(s => s.type_frais_id == opt.value);
                if (!info) return;

                const libelleBase = info.libelle;

                if (info.solde <= 0) {
                    opt.textContent = `${libelleBase} — ✅ Déjà soldé`;
                    opt.disabled = true;
                } else {
                    opt.textContent = `${libelleBase} — reste ${Number(info.solde).toLocaleString('fr-FR')} FCFA (sur ${Number(info.montant_du).toLocaleString('fr-FR')})`;
                    opt.disabled = false;
                }
            });
        });
}

document.getElementById('recherche_eleve').addEventListener('input', function () {
    const terme = this.value.toLowerCase().trim();

    if (!terme) {
        afficherListeEleves(elevesClasse);
        return;
    }

    const filtres = elevesClasse.filter(e =>
        (e.nom + ' ' + e.prenom).toLowerCase().includes(terme) ||
        (e.matricule ?? '').toLowerCase().includes(terme)
    );

    afficherListeEleves(filtres);
});
</script>

@endsection