<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'notes';

    protected $fillable = [
        'eleve_id',
        'matiere_id',
        'enseignant_id',
        'note',
        'note_max',
        'periode',
        'annee_scolaire',
        'observation',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(Utilisateur::class, 'enseignant_id');
    }
}