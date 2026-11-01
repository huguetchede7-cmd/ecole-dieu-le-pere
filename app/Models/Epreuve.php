<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Epreuve extends Model
{
    protected $table = 'epreuves';

    protected $fillable = [
        'examen_id',
        'niveau',
        'matiere_id',
        'fichier',
        'nom_original',
        'depose_par',
        'depose_en_retard',
        'fichier_final',
        'nom_final',
        'final_par',
        'final_le',
    ];

    protected $casts = [
        'depose_en_retard' => 'boolean',
        'final_le' => 'datetime',
    ];

    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

        // La personne qui a déposé la version prête à imprimer (la secrétaire)
    public function auteurFinal()
    {
        return $this->belongsTo(Utilisateur::class, 'final_par');
    }

    // Le fichier à imprimer : la version prête si elle existe, sinon l'original
    public function versionAImprimer(): array
    {
        return $this->fichier_final
            ? ['chemin' => $this->fichier_final, 'nom' => $this->nom_final]
            : ['chemin' => $this->fichier, 'nom' => $this->nom_original];
    }

    // La personne qui a déposé le fichier (l'enseignant, ou le directeur à sa place)
    public function auteur()
    {
        return $this->belongsTo(Utilisateur::class, 'depose_par');
    }
}