<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpreuveAcces extends Model
{
    protected $table = 'epreuve_acces';

    protected $fillable = [
        'epreuve_id',
        'utilisateur_id',
        'action',
        'version',
    ];

    public function epreuve()
    {
        return $this->belongsTo(Epreuve::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    // Note qui a ouvert ou téléchargé une épreuve. Un même geste répété en moins de 2 minutes n'est noté qu'une fois.
    public static function noter(Epreuve $epreuve, string $action, string $version): void
    {
        $dejaNote = static::where('epreuve_id', $epreuve->id)
            ->where('utilisateur_id', session('utilisateur_id'))
            ->where('action', $action)
            ->where('version', $version)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->exists();

        if ($dejaNote) {
            return;
        }

        static::create([
            'epreuve_id'     => $epreuve->id,
            'utilisateur_id' => session('utilisateur_id'),
            'action'         => $action,
            'version'        => $version,
        ]);
    }
}