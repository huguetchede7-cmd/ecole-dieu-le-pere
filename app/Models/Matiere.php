<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $table = 'matieres';

    protected $fillable = [
        'nom',
        'niveau'
    ];

    public function classes()
    {
        return $this->hasMany(Classe::class, 'niveau', 'niveau');
    }
}