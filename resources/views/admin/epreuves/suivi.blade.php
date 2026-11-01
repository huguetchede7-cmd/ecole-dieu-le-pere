@extends('layouts.app')

@section('title', 'Suivi des épreuves')
@section('page_title', 'Suivi des épreuves')

@section('content')

@if(session('success'))
<div style="background:#e6f4ea; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
✅ {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background:#fdecea; color:#c62828; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
{{ $errors->first() }}
</div>
@endif

@if($examens->isEmpty())
<div style="background:white; border-radius:12px; padding:40px; text-align:center; color:#999; font-size:14px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
    Aucune session d'examen pour cette année. Créez-en une dans « Examens et compositions ».
</div>
@else

<form method="GET" action="/admin/suivi-epreuves" style="margin-bottom:20px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <label style="font-size:14px; color:#666;">Session :</label>
    <select name="examen_id" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
        @foreach($examens as $x)
        <option value="{{ $x->id }}" {{ $examen && $examen->id === $x->id ? 'selected' : '' }}>{{ $x->titre }} — {{ $x->periode }}</option>
        @endforeach
    </select>
</form>

@if($examen)
@php
    [$libelle, $couleur, $fond] = $examen->phase();
    $retard = now('Africa/Porto-Novo')->toDateString() > $examen->date_limite_depot->toDateString();

    $ouvertes = $examen->epreuvesOuvertes();

    // Une épreuve par niveau et par matière (les classes d'un même niveau partagent la même épreuve)
    $attendues = 0;
    $deposees = 0;
    foreach ($groupes as $niveau => $classesDuNiveau) {
        foreach ($matieresParNiveau->get($niveau, collect()) as $m) {
            $attendues++;
            if ($epreuves->has($niveau . '|' . $m->id)) {
                $deposees++;
            }
        }
    }
    $manquantes = $attendues - $deposees;
@endphp

<div style="display:flex; gap:16px; flex-wrap:wrap; margin-bottom:20px;">
    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:18px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <div style="font-size:12px; color:#888;">Phase</div>
        <div style="margin-top:8px;">
            <span style="background:{{ $fond }}; color:{{ $couleur }}; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;">{{ $libelle }}</span>
        </div>
        <div style="font-size:12px; color:#888; margin-top:8px;">Dépôt avant le {{ $examen->date_limite_depot->format('d/m/Y') }}</div>
    </div>
    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:18px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <div style="font-size:12px; color:#888;">Épreuves attendues</div>
        <div style="font-size:26px; font-weight:700; color:#333; margin-top:6px;">{{ $attendues }}</div>
    </div>
    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:18px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <div style="font-size:12px; color:#888;">Déposées</div>
        <div style="font-size:26px; font-weight:700; color:#2e7d32; margin-top:6px;">{{ $deposees }}</div>
    </div>
    <div style="flex:1; min-width:180px; background:white; border-radius:12px; padding:18px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <div style="font-size:12px; color:#888;">Pas encore déposées</div>
        <div style="font-size:26px; font-weight:700; color:{{ $manquantes > 0 ? ($retard ? '#c62828' : '#e65100') : '#2e7d32' }}; margin-top:6px;">{{ $manquantes }}</div>
    </div>
</div>

@if($retard && $manquantes > 0)
<div style="background:#fdecea; color:#c62828; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
    ⚠️ La date limite de dépôt est dépassée et {{ $manquantes }} épreuve{{ $manquantes > 1 ? 's ne sont pas encore déposées' : ' n\'est pas encore déposée' }}.
</div>
@endif

@if($ouvertes)
<div style="background:#e6f4ea; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
    🔓 Les épreuves sont ouvertes
    @if($examen->ouverture_anticipee_le)
        (ouverture anticipée par {{ $examen->ouvreurAnticipe ? $examen->ouvreurAnticipe->nom : 'un responsable' }}
        le {{ $examen->ouverture_anticipee_le->timezone('Africa/Porto-Novo')->format('d/m/Y à H:i') }}).
    @else
        depuis le {{ $examen->dateOuverture()->format('d/m/Y') }}.
    @endif
</div>
@else
<div style="background:#eef1f6; color:#44546a; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px; line-height:1.7;">
    🔒 Les épreuves sont <strong>verrouillées jusqu'au {{ $examen->dateOuverture()->format('d/m/Y') }}</strong>.
    D'ici là, seul l'enseignant du niveau peut lire son épreuve : vous voyez uniquement l'état des dépôts.
    @if(in_array(session('utilisateur_role'), ['admin', 'directeur'], true))
    <form method="POST" action="/admin/suivi-epreuves/{{ $examen->id }}/ouvrir" onsubmit="return confirm('Ouvrir les épreuves maintenant ? Cette action est enregistrée à votre nom.')" style="display:inline; margin-left:10px;">
        @csrf
        <button type="submit" style="background:#44546a; color:white; border:none; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">Ouvrir maintenant</button>
    </form>
    @endif
</div>
@endif

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f8f9fa;">
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Niveau et classes</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Enseignants</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Déposées</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Détail par matière</th>
        </tr>
    </thead>
    <tbody>
        @forelse($groupes as $niveau => $classesDuNiveau)
        @php
            $matieres = $matieresParNiveau->get($niveau, collect());
            $nb = $matieres->count();
            $ok = $matieres->filter(fn($m) => $epreuves->has($niveau . '|' . $m->id))->count();
            $couleurProgression = ($nb > 0 && $ok === $nb) ? '#2e7d32' : ($retard ? '#c62828' : '#e65100');
        @endphp
        <tr style="border-top:1px solid #f0f0f0;">
            <td style="padding:14px 20px; font-size:14px; color:#333;">
                <strong>{{ $niveau }}</strong><br>
                <span style="font-size:12px; color:#888;">{{ $classesDuNiveau->pluck('nom')->implode(' + ') }}</span>
                @if($classesDuNiveau->count() > 1)
                    <br><span style="font-size:11px; color:#1a73e8;">épreuve commune</span>
                @endif
            </td>
            <td style="padding:14px 20px; font-size:13px; color:#555; line-height:1.6;">
                @foreach($classesDuNiveau as $c)
                    <div>
                        {{ $c->nom }} :
                        @if($c->enseignant)
                            {{ $c->enseignant->nom }} {{ $c->enseignant->prenom }}
                        @else
                            <span style="color:#c62828;">Aucun enseignant</span>
                        @endif
                    </div>
                @endforeach
            </td>
            <td style="padding:14px 20px; font-size:15px; font-weight:700; color:{{ $couleurProgression }};">
                {{ $ok }}/{{ $nb }}
            </td>
            <td style="padding:10px 20px;">
                @forelse($matieres as $m)
                    @php $ep = $epreuves->get($niveau . '|' . $m->id); @endphp
                    @if($ep && $ouvertes)
                        <span title="Déposée le {{ $ep->updated_at->timezone('Africa/Porto-Novo')->format('d/m/Y à H:i') }}{{ $ep->auteur ? ' par ' . $ep->auteur->nom . ' ' . $ep->auteur->prenom : '' }}"
                              style="display:inline-block; background:#e6f4ea; color:#2e7d32; padding:4px 10px; border-radius:20px; font-size:12px; margin:2px;">
                            ✅ <strong>{{ $m->nom }}</strong>{{ $ep->depose_en_retard ? ' ⚠️' : '' }}
                            @if($ep->fichier_final) <span style="background:#6a1b9a; color:white; padding:1px 7px; border-radius:10px; font-size:11px;">prête</span> @endif
                            · <a href="/admin/epreuves/{{ $ep->id }}/imprimer" target="_blank" style="color:#1a73e8; text-decoration:none; font-weight:600;">🖨️ Imprimer</a>
                            · <a href="/admin/epreuves/{{ $ep->id }}/telecharger" style="color:#1a73e8; text-decoration:none;">⬇️ Original</a>
                            @if($ep->fichier_final)
                            · <a href="/admin/epreuves/{{ $ep->id }}/telecharger?version=finale" style="color:#1a73e8; text-decoration:none;">⬇️ Version prête</a>
                            @endif
                        </span>
                    @elseif($ep)
                        <span title="Déposée le {{ $ep->updated_at->timezone('Africa/Porto-Novo')->format('d/m/Y à H:i') }}{{ $ep->auteur ? ' par ' . $ep->auteur->nom . ' ' . $ep->auteur->prenom : '' }} — verrouillée jusqu'au {{ $examen->dateOuverture()->format('d/m/Y') }}"
                              style="display:inline-block; background:#e6f4ea; color:#2e7d32; padding:4px 10px; border-radius:20px; font-size:12px; margin:2px;">
                            ✅ {{ $m->nom }} 🔒{{ $ep->depose_en_retard ? ' ⚠️' : '' }}
                        </span>
                    @else
                        <span style="display:inline-block; background:{{ $retard ? '#fdecea' : '#fff4e5' }}; color:{{ $retard ? '#c62828' : '#e65100' }}; padding:4px 10px; border-radius:20px; font-size:12px; margin:2px;">
                            ⏳ {{ $m->nom }}
                        </span>
                    @endif
                @empty
                    <span style="font-size:13px; color:#999;">Aucune matière pour ce niveau</span>
                @endforelse

                                @php $avecEpreuve = $matieres->filter(fn($m) => $epreuves->has($niveau . '|' . $m->id)); @endphp
                @if($ouvertes && $avecEpreuve->count() > 0 && in_array(session('utilisateur_role'), ['admin', 'directeur', 'secretaire'], true))
                <details style="margin-top:8px;">
                    <summary style="cursor:pointer; font-size:12px; color:#6a1b9a;">📎 Déposer la version prête à imprimer (PDF)</summary>
                    <form method="POST" action="/admin/epreuves/version-finale" enctype="multipart/form-data" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-top:8px;">
                        @csrf
                        <select name="epreuve_id" required style="padding:6px 10px; border:1px solid #ddd; border-radius:6px; font-size:12px;">
                            @foreach($avecEpreuve as $mm)
                            <option value="{{ $epreuves->get($niveau . '|' . $mm->id)->id }}">{{ $mm->nom }}</option>
                            @endforeach
                        </select>
                        <input type="file" name="fichier" accept=".pdf" required style="font-size:12px; max-width:220px;">
                        <button type="submit" style="background:#6a1b9a; color:white; border:none; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">Enregistrer</button>
                    </form>
                </details>
                @endif

                @php $manquantesNiveau = $matieres->filter(fn($m) => !$epreuves->has($niveau . '|' . $m->id)); @endphp
                @if(in_array(session('utilisateur_role'), ['admin', 'directeur'], true) && $manquantesNiveau->count() > 0)
                <details style="margin-top:8px;">
                    <summary style="cursor:pointer; font-size:12px; color:#1a73e8;">Déposer à la place des enseignants</summary>
                    <form method="POST" action="/admin/suivi-epreuves/deposer" enctype="multipart/form-data" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-top:8px;">
                        @csrf
                        <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                        <input type="hidden" name="niveau" value="{{ $niveau }}">
                        <select name="matiere_id" required style="padding:6px 10px; border:1px solid #ddd; border-radius:6px; font-size:12px;">
                            @foreach($manquantesNiveau as $mm)
                            <option value="{{ $mm->id }}">{{ $mm->nom }}</option>
                            @endforeach
                        </select>
                        <input type="file" name="fichier" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required style="font-size:12px; max-width:220px;">
                        <button type="submit" style="background:#1a73e8; color:white; border:none; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">Déposer</button>
                    </form>
                </details>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="padding:40px; text-align:center; color:#999; font-size:14px;">Aucune classe pour cette année scolaire.</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

<p style="font-size:12px; color:#888; margin-top:14px;">
    ✅ épreuve déposée · 🔒 verrouillée (pas encore ouverte) · ⚠️ déposée en retard · ⏳ pas encore déposée
</p>

@endif
@endif

@endsection