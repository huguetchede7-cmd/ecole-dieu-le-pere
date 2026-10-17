<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Paiement;

class LivretCaisseController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->query('mode', 'jour');
        if (!in_array($mode, ['jour', 'mois', 'annee'])) {
        $mode = 'jour';
        }

        // Valeurs par défaut (ou valeurs invalides → aujourd'hui)
        $date = $this->dateValide($request->query('date'), 'Y-m-d') ?? date('Y-m-d');
        $mois = $this->dateValide($request->query('mois'), 'Y-m') ?? date('Y-m');

        // Années scolaires proposées : de 2023 jusqu'à l'année prochaine
        $anneesDisponibles = [];
        for ($d = 2023; $d <= (int) date('Y') + 1; $d++) {
            $anneesDisponibles[] = $d . '-' . ($d + 1);
        }
        $anneeScolaire = $request->query('annee_scolaire');
        if (!in_array($anneeScolaire, $anneesDisponibles)) {
            $anneeScolaire = \App\Models\Parametre::anneeCourante();
        }

        $query = Paiement::with(['eleve', 'typeFrais', 'comptable', 'recu', 'inscription.classe']);

        if ($mode === 'jour') {
            $query->whereDate('date_paiement', $date);
            $libelle = 'Journée du ' . Carbon::parse($date)->format('d/m/Y');
        } elseif ($mode === 'mois') {
            $c = Carbon::createFromFormat('Y-m', $mois);
            $query->whereYear('date_paiement', $c->year)->whereMonth('date_paiement', $c->month);
            $libelle = 'Mois de ' . $c->translatedFormat('F Y');
        } else {
            $debut = (int) substr($anneeScolaire, 0, 4);
            $query->where(function ($q) use ($anneeScolaire, $debut) {
                $q->whereHas('inscription', fn($i) => $i->where('annee_scolaire', $anneeScolaire))
                  ->orWhere(function ($q2) use ($debut) {
                      $q2->whereNull('inscription_id')
                         ->whereBetween('date_paiement', [$debut . '-07-01', ($debut + 1) . '-06-30']);
                  });
            });
            $libelle = 'Année scolaire ' . $anneeScolaire;
        }

        $paiements = $query->orderBy('date_paiement')->orderBy('id')->get();

        $total = $paiements->sum('montant_paye');
        $nbRecus = $paiements->pluck('recu_id')->filter()->unique()->count();
        $nbEleves = $paiements->pluck('eleve_id')->unique()->count();
        $norm = fn($p) => str_replace(' ', '_', strtolower(\Illuminate\Support\Str::ascii($p->mode_paiement)));
        $totalEspeces = $paiements->filter(fn($p) => $norm($p) === 'especes')->sum('montant_paye');
        $totalMobileMoney = $paiements->filter(fn($p) => $norm($p) === 'mobile_money')->sum('montant_paye');
        $totalAutres = $total - $totalEspeces - $totalMobileMoney;

            return view('admin.livret-caisse.index', compact(
            'mode', 'date', 'mois', 'anneeScolaire', 'anneesDisponibles',
            'paiements', 'total', 'nbRecus', 'nbEleves', 'libelle',
            'totalEspeces', 'totalMobileMoney', 'totalAutres'
        ));
    }

    private function dateValide($valeur, $format)
    {
        if (!$valeur) {
            return null;
        }
        try {
            $c = Carbon::createFromFormat($format, $valeur);
            return $c && $c->format($format) === $valeur ? $valeur : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
