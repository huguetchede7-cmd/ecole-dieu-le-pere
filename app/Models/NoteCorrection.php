<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteCorrection extends Model
{
    protected $table = 'note_corrections';

    protected $fillable = [
        'note_id',
        'ancienne_note',
        'ancienne_note_max',
        'nouvelle_note',
        'nouvelle_note_max',
        'motif',
        'modifie_par',
    ];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function auteur()
    {
        return $this->belongsTo(Utilisateur::class, 'modifie_par');
    }
}