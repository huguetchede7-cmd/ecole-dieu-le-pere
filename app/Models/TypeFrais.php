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
    ];

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}