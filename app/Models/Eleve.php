<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory;

    protected $table = 'eleves';

    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'classe_id',           // ← Ajout important
        'annee_scolaire',      // ← Ajout important
        'nom_parent',
        'contact_parent',
        'adresse',
        'photo',
        'statut'
    ];

    // === RELATIONS ===

    /**
     * Relation directe avec la classe (utilisée dans index, edit, etc.)
     */
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relation avec les inscriptions (déjà existante)
     */
    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    /**
     * Classe actuelle de l'élève (via inscription active)
     */
    public function classeActuelle()
    {
        return $this->inscriptions()
                    ->where('statut', 'actif')
                    ->with('classe')
                    ->latest()
                    ->first();
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
}