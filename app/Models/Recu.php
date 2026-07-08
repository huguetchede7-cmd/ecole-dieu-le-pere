<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recu extends Model
{
    protected $table = 'recus';

    protected $fillable = [
        'paiement_id',
        'secretaire_id',
        'numero_recu',
        'date_emission',
    ];

    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    public function secretaire()
    {
        return $this->belongsTo(Utilisateur::class, 'secretaire_id');
    }
}