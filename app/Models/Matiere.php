<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $table = 'matieres';

    protected $fillable = [
        'nom',
        'classe_id'
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
}