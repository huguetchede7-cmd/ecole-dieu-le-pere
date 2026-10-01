<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FraisNiveau extends Model
{
    protected $table = 'frais_niveaux';

    protected $fillable = [
        'type_frais_id',
        'niveau',
        'montant',
    ];

    public function typeFrais()
    {
        return $this->belongsTo(TypeFrais::class);
    }
}