@extends('layouts.app')

@section('title', 'Ajouter des notes')
@section('page_title', 'Ajouter des notes')

@section('content')

<div style="max-width: 700px;">

@if($errors->any())
<div style="background: #fdecea; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
@foreach($errors->all() as $error)
<div>❌ {{ $error }}</div>
@endforeach
</div>
@endif

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
    <div>
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Classe</label>
        <select id="classe_id" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
            <option value="">-- Choisir --</option>
            @foreach($classes as $classe)
            <option value="{{ $classe->id }}" data-niveau="{{ $classe->niveau }}">
                {{ $classe->nom }} {{ $classe->niveau ? '(' . $classe->niveau . ')' : '' }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Matière</label>
        <select id="matiere_id" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
            <option value="">-- Choisir une classe d'abord --</option>
        </select>
    </div>

    <div>
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Trimestre</label>
        <select id="periode" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
            <option value="1er trimestre">1er trimestre</option>
            <option value="2eme trimestre">2ème trimestre</option>
            <option value="3eme trimestre">3ème trimestre</option>
        </select>
    </div>

    <div>
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Année scolaire</label>
        <input type="text" id="annee_scolaire" value="2025-2026" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
    </div>
</div>

<button type="button" id="btn-charger" onclick="chargerEleves()"
    style="background: #e8f0fe; color: #1a73e8; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; margin-bottom: 24px;">
    Afficher les élèves
</button>

<div id="zone-eleves"></div>

</div>
</div>

<script>
const matieresParNiveau = @json($matieres->groupBy('niveau'));

document.getElementById('classe_id').addEventListener('change', function () {
    const niveau = this.options[this.selectedIndex]?.getAttribute('data-niveau');
    const matiereSelect = document.getElementById('matiere_id');

    matiereSelect.innerHTML = '<option value="">-- Choisir --</option>';

    if (niveau && matieresParNiveau[niveau]) {
        matieresParNiveau[niveau].forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = m.nom;
            matiereSelect.appendChild(opt);
        });
    }

    document.getElementById('zone-eleves').innerHTML = '';
});

function chargerEleves() {
    const classeId = document.getElementById('classe_id').value;
    const matiereId = document.getElementById('matiere_id').value;
    const periode = document.getElementById('periode').value;
    const annee = document.getElementById('annee_scolaire').value;
    const zone = document.getElementById('zone-eleves');

    if (!classeId || !matiereId || !annee) {
        zone.innerHTML = '<p style="color:#c62828; font-size:13px;">Choisis une classe, une matière et une année scolaire.</p>';
        return;
    }

    zone.innerHTML = '<p style="color:#999; font-size:13px;">Chargement...</p>';

    fetch(`/admin/notes/eleves-note/${classeId}/${matiereId}/${encodeURIComponent(periode)}/${encodeURIComponent(annee)}`)
        .then(res => res.json())
        .then(eleves => {
            if (eleves.length === 0) {
                zone.innerHTML = '<p style="color:#999; font-size:13px;">Aucun élève inscrit dans cette classe.</p>';
                return;
            }

            let html = `
                <form method="POST" action="/admin/notes" id="form-notes">
                    @csrf
                    <input type="hidden" name="matiere_id" id="hidden_matiere_id">
                    <input type="hidden" name="periode" id="hidden_periode">
                    <input type="hidden" name="annee_scolaire" id="hidden_annee">

                    <table style="width:100%; border-collapse:collapse; font-size:14px; margin-bottom:20px;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:10px 12px; text-align:left; color:#666; font-size:13px;">Élève</th>
                                <th style="padding:10px 12px; text-align:left; color:#666; font-size:13px; width:120px;">Note</th>
                                <th style="padding:10px 12px; text-align:left; color:#666; font-size:13px; width:100px;">/ Max</th>
                            </tr>
                        </thead>
                        <tbody>`;

            eleves.forEach((e, index) => {
                html += `
                    <tr style="border-top:1px solid #f0f0f0;">
                        <td style="padding:8px 12px; color:#333;">${e.nom} ${e.prenom}</td>
                        <td style="padding:8px 12px;">
                            <input type="hidden" name="notes[${index}][eleve_id]" value="${e.eleve_id}">
                            <input type="number" step="0.01" min="0" name="notes[${index}][note]"
                                value="${e.note ?? ''}" tabindex="${index + 1}"
                                style="width:90px; padding:6px 10px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                        </td>
                        <td style="padding:8px 12px;">
                            <input type="number" step="0.01" min="1" name="notes[${index}][note_max]"
                                value="${e.note_max ?? 20}" tabindex="-1"
                                style="width:70px; padding:6px 10px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                        </td>
                    </tr>`;
            });

            html += `
                        </tbody>
                    </table>

                    <div style="display:flex; gap:12px;">
                        <button type="submit" style="background:#1a73e8; color:white; border:none; padding:12px 24px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                            ✅ Enregistrer toutes les notes
                        </button>
                    </div>
                </form>`;

            zone.innerHTML = html;

            document.getElementById('hidden_matiere_id').value = matiereId;
            document.getElementById('hidden_periode').value = periode;
            document.getElementById('hidden_annee').value = annee;

            const premierChamp = zone.querySelector('input[type="number"][tabindex="1"]');
            if (premierChamp) premierChamp.focus();
        })
        .catch(() => {
            zone.innerHTML = '<p style="color:#c62828; font-size:13px;">Erreur lors du chargement.</p>';
        });
}
</script>

@endsection