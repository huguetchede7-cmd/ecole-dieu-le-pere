@extends('layouts.app')

@section('title', 'Ajouter des absences')
@section('page_title', 'Ajouter des absences')

@section('content')

<div style="max-width: 750px;">

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
            <option value="{{ $classe->id }}">
                {{ $classe->nom }} {{ $classe->niveau ? '(' . $classe->niveau . ')' : '' }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Date</label>
        <input type="date" id="date_absence" value="{{ date('Y-m-d') }}" style="width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
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
function chargerEleves() {
    const classeId = document.getElementById('classe_id').value;
    const date = document.getElementById('date_absence').value;
    const zone = document.getElementById('zone-eleves');

    if (!classeId || !date) {
        zone.innerHTML = '<p style="color:#c62828; font-size:13px;">Choisis une classe et une date.</p>';
        return;
    }

    zone.innerHTML = '<p style="color:#999; font-size:13px;">Chargement...</p>';

    fetch(`/admin/absences/eleves-classe/${classeId}/${date}`)
        .then(res => res.json())
        .then(eleves => {
            if (eleves.length === 0) {
                zone.innerHTML = '<p style="color:#999; font-size:13px;">Aucun élève inscrit dans cette classe.</p>';
                return;
            }

            let html = `
                <form method="POST" action="/admin/absences" id="form-absences">
                    @csrf
                    <input type="hidden" name="date" id="hidden_date">

                    <table style="width:100%; border-collapse:collapse; font-size:14px; margin-bottom:20px;">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th style="padding:10px 12px; text-align:left; color:#666; font-size:13px;">Élève</th>
                                <th style="padding:10px 12px; text-align:center; color:#666; font-size:13px; width:110px;">Présent</th>
                                <th style="padding:10px 12px; text-align:center; color:#666; font-size:13px; width:110px;">Absent</th>
                                <th style="padding:10px 12px; text-align:center; color:#666; font-size:13px; width:110px;">Retard</th>
                                <th style="padding:10px 12px; text-align:left; color:#666; font-size:13px;">Motif</th>
                            </tr>
                        </thead>
                        <tbody>`;

            eleves.forEach((e, index) => {
                html += `
                    <tr style="border-top:1px solid #f0f0f0;">
                        <td style="padding:8px 12px; color:#333;">
                            ${e.nom} ${e.prenom}
                            <input type="hidden" name="absences[${index}][eleve_id]" value="${e.eleve_id}">
                        </td>
                        <td style="padding:8px 12px; text-align:center;">
                            <input type="radio" name="absences[${index}][statut]" value="present" ${e.statut === 'present' ? 'checked' : ''}>
                        </td>
                        <td style="padding:8px 12px; text-align:center;">
                            <input type="radio" name="absences[${index}][statut]" value="absent" ${e.statut === 'absent' ? 'checked' : ''}>
                        </td>
                        <td style="padding:8px 12px; text-align:center;">
                            <input type="radio" name="absences[${index}][statut]" value="retard" ${e.statut === 'retard' ? 'checked' : ''}>
                        </td>
                        <td style="padding:8px 12px;">
                            <input type="text" name="absences[${index}][motif]" value="${e.motif ?? ''}" placeholder="Optionnel"
                                style="width:100%; padding:6px 10px; border:1px solid #ddd; border-radius:6px; font-size:13px;">
                        </td>
                    </tr>`;
            });

            html += `
                        </tbody>
                    </table>

                    <div style="display:flex; gap:12px;">
                        <button type="submit" style="background:#1a73e8; color:white; border:none; padding:12px 24px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                            ✅ Enregistrer les absences
                        </button>
                    </div>
                </form>`;

            zone.innerHTML = html;
            document.getElementById('hidden_date').value = date;
        })
        .catch(() => {
            zone.innerHTML = '<p style="color:#c62828; font-size:13px;">Erreur lors du chargement.</p>';
        });
}
</script>

@endsection