<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory;

    protected $table = 'eleves';

    protected $fillable = [ 
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'nom_parent',
        'contact_parent',
        'adresse',
        'photo',
    ];

    // === RELATIONS ===

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    /**
     * Inscription active la plus récente (relation Eloquent, eager-loadable)
     */
    public function inscriptionActuelle()
    {
        return $this->hasOne(Inscription::class)
            ->where('statut', 'actif')
            ->latestOfMany();
    }

    // === ACCESSEURS ===

    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }

    public function getAgeAttribute()
    {
        return $this->date_naissance
            ? \Carbon\Carbon::parse($this->date_naissance)->age
            : null;
    }

    public function getClasseActuelleAttribute()
    {
        return $this->inscriptionActuelle?->classe;
    }

    public static function genererMatricule(): string
{
    $annee = date('Y');
    $dernier = self::where('matricule', 'like', "MAT-{$annee}-%")
        ->orderByDesc('matricule')
        ->value('matricule');

    $numero = $dernier ? ((int) substr($dernier, -5)) + 1 : 1;

    return 'MAT-' . $annee . '-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
}
}
