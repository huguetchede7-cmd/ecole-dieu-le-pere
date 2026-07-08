<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $table = 'paiements';

    protected $fillable = [
        'eleve_id',
        'type_frais_id',
        'comptable_id',
        'montant_paye',
        'date_paiement',
        'mode_paiement',
        'observation',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function typeFrais()
    {
        return $this->belongsTo(TypeFrais::class);
    }

    public function comptable()
    {
        return $this->belongsTo(Utilisateur::class, 'comptable_id');
    }
}