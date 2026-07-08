<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    //
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $table = 'absences';

    protected $fillable = [
        'eleve_id',
        'enseignant_id',
        'date',
        'statut',
        'motif',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(Utilisateur::class, 'enseignant_id');
    }
}