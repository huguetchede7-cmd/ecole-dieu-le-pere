<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\EpreuvesManquantesMail;
use App\Models\Examen;
use App\Models\Utilisateur;

class RappelEpreuves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epreuves:rappel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie un e-mail de rappel aux enseignants qui n\'ont pas déposé leurs épreuves';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $enseignants = Utilisateur::where('role', 'enseignant')
    ->where('statut', 'actif')
    ->whereNotNull('email')
    ->get();

$envoyes = 0;

foreach ($enseignants as $enseignant) {
    $classe = $enseignant->classe;

    if (!$classe) {
        continue;
    }

    $alertes = Examen::alertesEpreuves($classe);

    if (count($alertes) === 0) {
        continue;
    }

    Mail::to($enseignant->email)->send(new EpreuvesManquantesMail($enseignant, $alertes));
    $envoyes++;
}

$this->info($envoyes . ' e-mail(s) de rappel envoyé(s).');
    }
}
