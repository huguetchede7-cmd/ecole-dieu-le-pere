@php
    $roleAlerte = session('utilisateur_role');
    $alertesEpreuves = [];

    if (in_array($roleAlerte, ['admin', 'directeur'], true)) {
        $alertesEpreuves = \App\Models\Examen::alertesEpreuves();
    } elseif ($roleAlerte === 'enseignant') {
        $maClasseAlerte = \App\Models\Classe::where('enseignant_id', session('utilisateur_id'))->first();
        $alertesEpreuves = $maClasseAlerte ? \App\Models\Examen::alertesEpreuves($maClasseAlerte) : [];
    }
@endphp

@foreach($alertesEpreuves as $a)
@php
    $examenA = $a['examen'];
    $jours = $a['jours'];
    $rouge = $a['en_retard'];
    $nb = count($a['manquantes']);

    if ($jours > 1) {
        $quand = 'La composition commence dans ' . $jours . ' jours';
    } elseif ($jours === 1) {
        $quand = 'La composition commence demain';
    } elseif ($jours === 0) {
        $quand = 'La composition commence aujourd\'hui';
    } else {
        $quand = 'La composition est en cours';
    }

    // Pour le directeur : regrouper les matières manquantes par niveau
    $parNiveau = collect($a['manquantes'])->groupBy('niveau');
@endphp

<div style="background:{{ $rouge ? '#fdecea' : '#fff4e5' }}; border-radius:12px; padding:20px 24px; margin-bottom:20px; border-left:4px solid {{ $rouge ? '#c62828' : '#e65100' }};">
    <h3 style="font-size:16px; color:{{ $rouge ? '#c62828' : '#e65100' }}; margin-bottom:8px;">
        📄 {{ $nb }} épreuve{{ $nb > 1 ? 's' : '' }} pas encore déposée{{ $nb > 1 ? 's' : '' }} : {{ $examenA->titre }}
    </h3>

    <div style="font-size:14px; color:#555; line-height:1.7;">
        {{ $quand }} (du {{ $examenA->date_debut->format('d/m/Y') }} au {{ $examenA->date_fin->format('d/m/Y') }}).
        Date limite de dépôt : <strong>{{ $examenA->date_limite_depot->format('d/m/Y') }}</strong>
        @if($rouge) — <strong style="color:#c62828;">dépassée</strong> @endif
    </div>

    @if($roleAlerte === 'enseignant')
        <div style="font-size:14px; color:#555; margin-top:8px;">
            Matières à déposer :
            <strong>{{ collect($a['manquantes'])->map(fn($x) => $x['matiere']->nom)->implode(', ') }}</strong>
        </div>
        <a href="/admin/epreuves" style="display:inline-block; margin-top:14px; background:{{ $rouge ? '#c62828' : '#e65100' }}; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">
            Déposer mes épreuves →
        </a>
    @else
        <div style="font-size:14px; color:#555; margin-top:8px; line-height:1.8;">
            @foreach($parNiveau->take(6) as $niveau => $liste)
                <div>
                    <strong>{{ $niveau }}</strong>
                    ({{ $liste->first()['classes']->map(fn($c) => $c->nom . ($c->enseignant ? ' — ' . $c->enseignant->nom : ''))->implode(' · ') }})
                    : {{ $liste->map(fn($x) => $x['matiere']->nom)->implode(', ') }}
                </div>
            @endforeach
            @if($parNiveau->count() > 6)
                <div style="font-size:13px; color:#777;">… et {{ $parNiveau->count() - 6 }} autre(s) niveau(x).</div>
            @endif
        </div>
        <a href="/admin/suivi-epreuves?examen_id={{ $examenA->id }}" style="display:inline-block; margin-top:14px; background:{{ $rouge ? '#c62828' : '#e65100' }}; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">
            Voir le suivi des épreuves →
        </a>
    @endif
</div>
@endforeach