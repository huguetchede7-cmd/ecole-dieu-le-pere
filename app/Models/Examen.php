<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    protected $table = 'examens';

    protected $fillable = [
        'titre',
        'type',
        'periode',
        'annee_scolaire',
        'date_limite_depot',
        'date_debut',
        'date_fin',
        'date_limite_notes',
        'consignes',
        'cree_par',
        'date_ouverture_impression',
        'ouverture_anticipee_par',
        'ouverture_anticipee_le',
    ];

    protected $casts = [
        'date_limite_depot' => 'date',
        'date_debut'        => 'date',
        'date_fin'          => 'date',
        'date_limite_notes' => 'date',
        'date_ouverture_impression' => 'date',
        'ouverture_anticipee_le'    => 'datetime',
    ];

    public function createur()
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par');
    }

        // La personne qui a ouvert les épreuves avant la date prévue (directeur ou admin)
    public function ouvreurAnticipe()
    {
        return $this->belongsTo(Utilisateur::class, 'ouverture_anticipee_par');
    }

    // Date à partir de laquelle les épreuves peuvent être lues : celle réglée pour la session, sinon 2 jours avant la composition
    public function dateOuverture(): \Carbon\Carbon
    {
        return $this->date_ouverture_impression
            ? $this->date_ouverture_impression->copy()
            : $this->date_debut->copy()->subDays(2);
    }

    // Les épreuves sont-elles ouvertes (lisibles par la direction et la secrétaire) ?
    public function epreuvesOuvertes(): bool
    {
        if ($this->ouverture_anticipee_le) {
            return true;
        }

        return now('Africa/Porto-Novo')->toDateString() >= $this->dateOuverture()->toDateString();
    }

        // Alertes de dépôt : sessions dont la composition approche (dès 2 semaines avant) et qui ont des épreuves manquantes.
    // Sans classe : toutes les classes de l'année (directeur, admin). Avec une classe : seulement celle de l'enseignant.
        public static function alertesEpreuves(?Classe $classe = null): array
    {
        $auj = \Carbon\Carbon::parse(now('Africa/Porto-Novo')->toDateString());
        $annee = Parametre::anneeCourante();

        $classes = $classe ? collect([$classe]) : Classe::where('annee_scolaire', $annee)->with('enseignant')->get();

        // Une épreuve par niveau : les classes d'un même niveau (CI A, CI B) partagent la même épreuve
        $classesParNiveau = $classes->groupBy('niveau');
        $matieresParNiveau = Matiere::orderBy('nom')->get()->groupBy('niveau');

        $alertes = [];

        foreach (static::where('annee_scolaire', $annee)->orderBy('date_debut')->get() as $examen) {
            $debut = \Carbon\Carbon::parse($examen->date_debut->toDateString());
            $fin = \Carbon\Carbon::parse($examen->date_fin->toDateString());
            $jours = (int) $auj->diffInDays($debut, false);

            // Alerte dès 2 semaines avant la composition, jusqu'à la fin de la composition
            if ($jours > 14 || $auj->gt($fin)) {
                continue;
            }

            $deposees = Epreuve::where('examen_id', $examen->id)->get()
                ->map(fn($e) => $e->niveau . '|' . $e->matiere_id)
                ->flip();

            $manquantes = [];
            foreach ($classesParNiveau as $niveau => $groupe) {
                foreach ($matieresParNiveau->get($niveau, collect()) as $m) {
                    if (!$deposees->has($niveau . '|' . $m->id)) {
                        $manquantes[] = ['niveau' => $niveau, 'classes' => $groupe, 'matiere' => $m];
                    }
                }
            }

            if (count($manquantes) > 0) {
                $alertes[] = [
                    'examen'     => $examen,
                    'jours'      => $jours,
                    'en_retard'  => $auj->gt(\Carbon\Carbon::parse($examen->date_limite_depot->toDateString())),
                    'manquantes' => $manquantes,
                ];
            }
        }

        return $alertes;
    }

    // Phase actuelle de la session, selon la date du jour (heure du Bénin)
    public function phase(): array
    {
        $aujourdhui = now('Africa/Porto-Novo')->toDateString();

        if ($aujourdhui <= $this->date_limite_depot->toDateString()) {
            return ['Dépôt des épreuves', '#1a73e8', '#e8f0fe'];
        }

        if ($aujourdhui < $this->date_debut->toDateString()) {
            return ['Impression', '#6a1b9a', '#f3e5f5'];
        }

        if ($aujourdhui <= $this->date_fin->toDateString()) {
            return ['Composition', '#e65100', '#fff4e5'];
        }

        if ($aujourdhui <= $this->date_limite_notes->toDateString()) {
            return ['Correction et saisie des notes', '#2e7d32', '#e6f4ea'];
        }

        return ['Terminé', '#555555', '#eeeeee'];
    }
}
