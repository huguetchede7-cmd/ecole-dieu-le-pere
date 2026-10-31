<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Epreuve extends Model
{
    protected $table = 'epreuves';

    protected $fillable = [
        'examen_id',
        'classe_id',
        'matiere_id',
        'fichier',
        'nom_original',
        'depose_par',
        'depose_en_retard',
    ];

    protected $casts = [
        'depose_en_retard' => 'boolean',
    ];

    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    // La personne qui a déposé le fichier (l'enseignant, ou le directeur à sa place)
    public function auteur()
    {
        return $this->belongsTo(Utilisateur::class, 'depose_par');
    }
}