<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $table = 'parametres';

    protected $fillable = ['cle', 'valeur'];

    // Valeurs utilisées tant que rien n'est enregistré (identiques à celles qui étaient écrites en dur)
    public const DEFAUTS = [
        'ecole_nom' => 'École Primaire Privée Dieu le Père',
        'ecole_ministere' => 'Ministère des Enseignements Maternels et Primaires',
        'ecole_autorisation' => '049/MEMP/CAB/DC/SGM/DPP/DGS/DEPES/SA/2005',
        'ecole_telephone' => '23767777',
        'ecole_bp' => '08 Parakou',
        'ecole_email' => 'dieulepere@yahoo.fr',
    ];

    public static function valeur(string $cle)
    {
        static $cache = null;

        if ($cache === null) {
            $cache = static::pluck('valeur', 'cle')->all();
        }

        $v = $cache[$cle] ?? null;

        return ($v === null || $v === '') ? (self::DEFAUTS[$cle] ?? null) : $v;
    }

    // Toutes les informations de l'école d'un coup : $ecole['nom'], $ecole['telephone']...
    public static function ecole(): array
    {
        return [
            'nom' => static::valeur('ecole_nom'),
            'ministere' => static::valeur('ecole_ministere'),
            'autorisation' => static::valeur('ecole_autorisation'),
            'telephone' => static::valeur('ecole_telephone'),
            'bp' => static::valeur('ecole_bp'),
            'email' => static::valeur('ecole_email'),
        ];
    }

    // Année scolaire en cours : réglage enregistré, sinon calculée (rentrée en juillet)
    public static function anneeCourante(): string
    {
        $v = static::valeur('annee_scolaire_courante');

        if ($v) {
            return $v;
        }

        $debut = (int) date('n') >= 7 ? (int) date('Y') : (int) date('Y') - 1;

        return $debut . '-' . ($debut + 1);
    }
}
