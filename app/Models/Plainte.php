<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plainte extends Model
{
    protected $table = 'plaintes';

    protected $fillable = [
        'eleve_id',
        'secretaire_id',
        'description',
        'statut',
        'reponse',
        'date_plainte',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function secretaire()
    {
        return $this->belongsTo(Utilisateur::class, 'secretaire_id');
    }
}