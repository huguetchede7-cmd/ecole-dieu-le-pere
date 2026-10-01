<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeFrais extends Model
{
    protected $table = 'types_frais';

    protected $fillable = [
        'libelle',
        'montant',
        'description',
        'varie_par_niveau',
        'categorie',
    ];

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function fraisNiveaux()
    {
        return $this->hasMany(FraisNiveau::class);
    }

    public function montantPour($niveau)
    {
        if (!$this->varie_par_niveau) {
            return $this->montant;
        }

        return $this->fraisNiveaux->firstWhere('niveau', $niveau)?->montant;
    }
}