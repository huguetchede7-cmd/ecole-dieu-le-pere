@extends('layouts.app')

@section('title', 'Suivi des paiements')
@section('page_title', 'Suivi des paiements')

@section('content')

<div style="max-width: 850px;">

@if($errors->any())
<div style="background: #fdecea; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
@foreach($errors->all() as $error)
<div>❌ {{ $error }}</div>
@endforeach
</div>
@endif

<div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px;">
    <p style="font-size: 13px; color: #666; margin: 0 0 12px;">
        Frais ponctuels (cantine, examens, cotisations…), payables en une seule tranche. Chaque paiement génère un reçu.
    </p>
    <input type="text" id="recherche" placeholder="Rechercher un élève par nom, prénom ou matricule (2 lettres minimum)..." autocomplete="off"
        style="width: 100%; padding: 12px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
    <div id="resultats" style="margin-top: 10px; border: 1px solid #ddd; border-radius: 8px; max-height: 260px; overflow-y: auto; display: none;"></div>
</div>

<div id="fiche" style="display: none;"></div>

</div>

<!-- Fenêtre d'encaissement -->
<div id="modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:26px; width:100%; max-width:420px; margin:16px;">
        <h3 style="margin:0 0 4px; font-size:17px;">Enregistrer un paiement</h3>
        <p id="modal-info" style="margin:0 0 16px; font-size:13px; color:#666;"></p>

        <form method="POST" action="{{ url('/admin/suivi-paiements/encaisser') }}">
            @csrf
            <input type="hidden" name="eleve_id" id="m-eleve">
            <input type="hidden" name="type_frais_id" id="m-type">

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Montant (FCFA)</label>
                <input type="text" id="m-montant" readonly
                    style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; background:#f5f5f5; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Mode de paiement</label>
                <select name="mode_paiement" required
                    style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;">
                    <option value="especes">Espèces</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
            </div>
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Date</label>
                <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" required
                    style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:6px;">Observation (optionnel)</label>
                <input type="text" name="observation" maxlength="255"
                    style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; box-sizing:border-box;">
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="fermerModal()"
                    style="padding:10px 18px; border:1px solid #ddd; background:white; border-radius:8px; cursor:pointer; font-size:14px;">Annuler</button>
                <button type="submit" id="m-submit"
                    style="padding:10px 18px; border:none; background:#e8a317; color:white; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE = '{{ url('/admin/suivi-paiements') }}';
const URL_RECUS = '{{ url('/admin/recus') }}';
let minuteur = null;
let eleveCourant = null;

function esc(s) {
    const d = document.createElement('div');
    d.textContent = s === null || s === undefined ? '' : s;
    return d.innerHTML;
}
function fcfa(n) {
    return Number(n).toLocaleString('fr-FR') + ' FCFA';
}

document.getElementById('recherche').addEventListener('input', function () {
    clearTimeout(minuteur);
    const q = this.value.trim();
    const zone = document.getElementById('resultats');
    if (q.length < 2) { zone.style.display = 'none'; return; }

    minuteur = setTimeout(async () => {
        const r = await fetch(BASE + '/rechercher?q=' + encodeURIComponent(q));
        const eleves = await r.json();
        if (!eleves.length) {
            zone.innerHTML = '<div style="padding:12px; font-size:13px; color:#999;">Aucun élève trouvé.</div>';
        } else {
            zone.innerHTML = eleves.map(e =>
                '<div class="ligne-eleve" data-id="' + e.id + '" style="padding:10px 14px; cursor:pointer; border-bottom:1px solid #eee; font-size:14px;">'
                + '<strong>' + esc(e.nom) + ' ' + esc(e.prenom) + '</strong>'
                + ' <span style="color:#888; font-size:12px;">' + esc(e.matricule) + ' · ' + esc(e.classe) + '</span></div>'
            ).join('');
        }
        zone.style.display = 'block';
    }, 250);
});

document.getElementById('resultats').addEventListener('click', function (ev) {
    const ligne = ev.target.closest('.ligne-eleve');
    if (!ligne) return;
    chargerFiche(ligne.dataset.id);
    this.style.display = 'none';
});

async function chargerFiche(id) {
    const zone = document.getElementById('fiche');
    const r = await fetch(BASE + '/fiche/' + id);
    const d = await r.json();

    if (!r.ok) {
        zone.innerHTML = '<div style="background:#fdecea; color:#c62828; padding:12px 16px; border-radius:8px; font-size:14px;">' + esc(d.message || 'Erreur') + '</div>';
        zone.style.display = 'block';
        return;
    }

    eleveCourant = d.eleve;

    let html = '<div style="background:white; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">';
    html += '<h3 style="margin:0 0 4px; font-size:17px;">Fiche — ' + esc(d.eleve.nom) + ' ' + esc(d.eleve.prenom) + '</h3>';
    html += '<p style="margin:0 0 16px; font-size:13px; color:#666;">' + esc(d.eleve.matricule) + ' · ' + esc(d.eleve.classe) + '</p>';

    html += '<div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:18px;">';
    html += carte('Total dû', fcfa(d.total_du), '#333');
    html += carte('Total payé', fcfa(d.total_paye), '#2e7d32');
    html += carte('Reste', fcfa(d.reste), '#c62828');
    html += '</div>';

    if (!d.frais.length) {
        html += '<div style="font-size:13px; color:#999;">Aucun frais ponctuel défini pour ce niveau.</div>';
    } else {
        html += '<table style="width:100%; border-collapse:collapse; font-size:14px;">';
        html += '<thead><tr style="text-align:left; border-bottom:2px solid #eee; color:#666; font-size:12px; text-transform:uppercase;">'
              + '<th style="padding:8px;">Frais</th><th style="padding:8px;">Montant</th><th style="padding:8px;">Statut</th><th style="padding:8px; text-align:right;">Action</th></tr></thead><tbody>';
        d.frais.forEach((f, i) => {
            html += '<tr style="border-bottom:1px solid #f0f0f0;">';
            html += '<td style="padding:10px 8px;">' + esc(f.libelle) + '</td>';
            html += '<td style="padding:10px 8px;">' + fcfa(f.montant) + '</td>';
            if (f.paye) {
                html += '<td style="padding:10px 8px;"><span style="background:#e8f5e9; color:#2e7d32; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">✔ Payé</span></td>';
                html += '<td style="padding:10px 8px; text-align:right;"><a href="' + URL_RECUS + '/' + f.recu_id + '" style="color:#1a56a0; font-size:13px;">Voir le reçu</a></td>';
            } else {
                html += '<td style="padding:10px 8px;"><span style="background:#fff3e0; color:#e65100; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">Non payé</span></td>';
                html += '<td style="padding:10px 8px; text-align:right;"><button type="button" class="btn-encaisser" data-i="' + i + '" style="padding:7px 14px; border:none; background:#e8a317; color:white; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;">Encaisser</button></td>';
            }
            html += '</tr>';
        });
        html += '</tbody></table>';
    }
    html += '</div>';

    zone.innerHTML = html;
    zone.style.display = 'block';

    zone.querySelectorAll('.btn-encaisser').forEach(b => {
        b.addEventListener('click', () => ouvrirModal(d.frais[b.dataset.i]));
    });
}

function carte(titre, valeur, couleur) {
    return '<div style="flex:1; min-width:150px; background:#f8f9fb; border-radius:10px; padding:12px 16px;">'
         + '<div style="font-size:12px; color:#888;">' + titre + '</div>'
         + '<div style="font-size:18px; font-weight:700; color:' + couleur + ';">' + valeur + '</div></div>';
}

function ouvrirModal(frais) {
    document.getElementById('m-eleve').value = eleveCourant.id;
    document.getElementById('m-type').value = frais.type_frais_id;
    document.getElementById('m-montant').value = fcfa(frais.montant);
    document.getElementById('modal-info').textContent = frais.libelle + ' — ' + eleveCourant.nom + ' ' + eleveCourant.prenom;
    document.getElementById('m-submit').disabled = false;
    document.getElementById('modal').style.display = 'flex';
}
function fermerModal() {
    document.getElementById('modal').style.display = 'none';
}

// Évite le double clic (un seul reçu par paiement)
document.querySelector('#modal form').addEventListener('submit', function () {
    document.getElementById('m-submit').disabled = true;
});
</script>

@endsection
