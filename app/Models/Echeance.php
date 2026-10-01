<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Echeance extends Model
{
    protected $table = 'echeances';

    protected $fillable = [
        'libelle',
        'annee_scolaire',
        'date_limite',
        'montant',
    ];
}