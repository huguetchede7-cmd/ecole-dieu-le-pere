<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recu extends Model
{
    protected $table = 'recus';

    protected $fillable = [
        'inscription_id',
        'secretaire_id',
        'numero_recu',
        'date_emission',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function secretaire()
    {
        return $this->belongsTo(Utilisateur::class, 'secretaire_id');
    }

    public function getMontantTotalAttribute()
    {
        return $this->paiements->sum('montant_paye');
    }
}