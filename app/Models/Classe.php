<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'nom',
        'niveau',
        'capacite',
        'annee_scolaire'
    ];

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function matieres()
    {
        return $this->hasMany(Matiere::class);
    }

    public function effectif()
    {
        return $this->inscriptions()->where('statut', 'actif')->count();
    }
}