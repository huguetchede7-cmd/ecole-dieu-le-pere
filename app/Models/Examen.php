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
    ];

    protected $casts = [
        'date_limite_depot' => 'date',
        'date_debut'        => 'date',
        'date_fin'          => 'date',
        'date_limite_notes' => 'date',
    ];

    public function createur()
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par');
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
