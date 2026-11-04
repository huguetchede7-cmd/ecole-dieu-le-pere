<p>{{ $enseignant->civilite }} {{ $enseignant->nom }},</p>

<p>Voici un rappel concernant le dépôt des épreuves de composition.</p>

@foreach($alertes as $alerte)
    <h3>{{ $alerte['examen']->titre }}</h3>
    <p>
        Composition à partir du {{ $alerte['examen']->date_debut->format('d/m/Y') }}.<br>
        Date limite de dépôt : {{ $alerte['examen']->date_limite_depot->format('d/m/Y') }}
        @if($alerte['en_retard'])
            <strong>(date dépassée)</strong>
        @endif
    </p>

    <p>Épreuves pas encore déposées :</p>
    <ul>
        @foreach($alerte['manquantes'] as $m)
            <li>{{ $m['matiere']->nom }} ({{ $m['niveau'] }})</li>
        @endforeach
    </ul>
@endforeach

<p>Merci de les déposer dès que possible depuis la page « Mes épreuves ».</p>

<p>École Dieu le Père</p>