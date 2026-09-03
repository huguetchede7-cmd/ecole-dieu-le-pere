<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    protected $table = 'inscriptions';

    protected $fillable = [
        'eleve_id',
        'classe_id',
        'annee_scolaire',
        'date_inscription',
        'statut',
        'decision'
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function recu()
    {
        return $this->hasOne(Recu::class);
    }
}