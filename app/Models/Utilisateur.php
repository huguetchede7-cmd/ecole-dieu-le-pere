<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    protected $table = 'utilisateurs';

    protected $fillable = [
        'nom',
        'prenom',
        'civilite',
        'email',
        'mot_de_passe',
        'role',
        'statut',
        'telephone'
    ];

    protected $hidden = [
        'mot_de_passe'
    ];
        // Classe confiée à cet enseignant (une seule)
    public function classe()
    {
        return $this->hasOne(Classe::class, 'enseignant_id');
    }
}