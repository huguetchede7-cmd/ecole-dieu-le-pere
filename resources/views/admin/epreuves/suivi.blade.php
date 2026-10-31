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

    $attendues = 0;
    $deposees = 0;
    foreach ($classes as $c) {
        foreach ($matieresParNiveau->get($c->niveau, collect()) as $m) {
            $attendues++;
            if ($epreuves->has($c->id . '-' . $m->id)) {
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

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f8f9fa;">
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Classe</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Enseignant</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Déposées</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Détail par matière</th>
        </tr>
    </thead>
    <tbody>
        @forelse($classes as $c)
        @php
            $matieres = $matieresParNiveau->get($c->niveau, collect());
            $nb = $matieres->count();
            $ok = $matieres->filter(fn($m) => $epreuves->has($c->id . '-' . $m->id))->count();
            $couleurProgression = ($nb > 0 && $ok === $nb) ? '#2e7d32' : ($retard ? '#c62828' : '#e65100');
        @endphp
        <tr style="border-top:1px solid #f0f0f0;">
            <td style="padding:14px 20px; font-size:14px; color:#333;">
                <strong>{{ $c->nom }}</strong><br>
                <span style="font-size:12px; color:#888;">{{ $c->niveau }}</span>
            </td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">
                @if($c->enseignant)
                    {{ $c->enseignant->nom }} {{ $c->enseignant->prenom }}
                @else
                    <span style="color:#c62828;">Aucun enseignant</span>
                @endif
            </td>
            <td style="padding:14px 20px; font-size:15px; font-weight:700; color:{{ $couleurProgression }};">
                {{ $ok }}/{{ $nb }}
            </td>
            <td style="padding:10px 20px;">
                @forelse($matieres as $m)
                    @php $ep = $epreuves->get($c->id . '-' . $m->id); @endphp
                    @if($ep)
                        <a href="/admin/epreuves/{{ $ep->id }}/telecharger"
                           title="Déposée le {{ $ep->updated_at->timezone('Africa/Porto-Novo')->format('d/m/Y à H:i') }}{{ $ep->auteur ? ' par ' . $ep->auteur->nom . ' ' . $ep->auteur->prenom : '' }}"
                           style="display:inline-block; background:#e6f4ea; color:#2e7d32; padding:4px 10px; border-radius:20px; font-size:12px; text-decoration:none; margin:2px;">
                            ✅ {{ $m->nom }}{{ $ep->depose_en_retard ? ' ⚠️' : '' }}
                        </a>
                    @else
                        <span style="display:inline-block; background:{{ $retard ? '#fdecea' : '#fff4e5' }}; color:{{ $retard ? '#c62828' : '#e65100' }}; padding:4px 10px; border-radius:20px; font-size:12px; margin:2px;">
                            ⏳ {{ $m->nom }}
                        </span>
                    @endif
                @empty
                    <span style="font-size:13px; color:#999;">Aucune matière pour ce niveau</span>
                @endforelse
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
    ✅ épreuve déposée (cliquer pour la télécharger) · ⚠️ déposée en retard · ⏳ pas encore déposée
</p>

@endif
@endif

@endsection