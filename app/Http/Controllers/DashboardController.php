<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classe;
use App\Models\Utilisateur;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\TypeFrais;
use App\Models\Eleve;
use App\Models\Echeance;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $anneeScolaire = $request->query('annee_scolaire', '2025-2026');

        $classes = Classe::where('annee_scolaire', $anneeScolaire)->get();
        $totalClasses = $classes->count();
        $totalEnseignants = Utilisateur::where('role', 'enseignant')->count();

        $inscriptionsActives = Inscription::where('annee_scolaire', $anneeScolaire)
            ->where('statut', 'actif')
            ->with(['eleve', 'classe'])
            ->get()
            ->filter(fn($i) => $i->eleve && $i->classe);

        $fraisInscription = TypeFrais::where('categorie', 'inscription')->with('fraisNiveaux')->first();
        $fraisReinscription = TypeFrais::where('categorie', 'reinscription')->with('fraisNiveaux')->first();
        $fraisScolarite = TypeFrais::where('categorie', 'scolarite')->with('fraisNiveaux')->first();

        $montantAttenduTotal = 0;
        $montantEncaisseTotal = 0;
        $elevesEnRetard = [];

        foreach ($inscriptionsActives as $inscription) {
            $niveau = $inscription->classe->niveau;

            // Est-ce une inscription (1ère fois) ou une réinscription pour cet élève ?
            $estPremiereInscription = Inscription::where('eleve_id', $inscription->eleve_id)
                ->where('annee_scolaire', '<', $anneeScolaire)
                ->doesntExist();

            $fraisBase = $estPremiereInscription ? $fraisInscription : $fraisReinscription;
            $montantBase = $fraisBase?->montantPour($niveau) ?? $fraisBase?->montant ?? 0;
            $montantScolarite = $fraisScolarite?->montantPour($niveau) ?? $fraisScolarite?->montant ?? 0;

            $montantDu = $montantBase + $montantScolarite;

            $montantPaye = Paiement::where('eleve_id', $inscription->eleve_id)
                ->whereHas('typeFrais', function ($q) {
                    $q->whereIn('categorie', ['inscription', 'reinscription', 'scolarite']);
                })
                ->whereYear('date_paiement', '>=', substr($anneeScolaire, 0, 4))
                ->sum('montant_paye');

            $solde = max(0, $montantDu - $montantPaye);

            $montantAttenduTotal += $montantDu;
            $montantEncaisseTotal += $montantPaye;
            $montantsPayesParEleve[$inscription->eleve_id] = $montantPaye;

            if ($solde > 0) {
                $elevesEnRetard[] = [
                    'nom' => $inscription->eleve->nom,
                    'prenom' => $inscription->eleve->prenom,
                    'classe' => $inscription->classe->nom,
                    'solde' => $solde,
                ];
            }
        }

        $soldeRestant = $montantAttenduTotal - $montantEncaisseTotal;

usort($elevesEnRetard, fn($a, $b) => $b['solde'] <=> $a['solde']);

$alertesEcheances = Echeance::where('annee_scolaire', $anneeScolaire)
    ->whereDate('date_limite', '<=', now()->addDays(7))
    ->orderBy('date_limite')
    ->get()
    ->map(function ($echeance) use ($montantsPayesParEleve) {
        $nbEnRetard = collect($montantsPayesParEleve)
            ->filter(fn($montant) => $montant < $echeance->montant)
            ->count();

        return [
            'libelle' => $echeance->libelle,
            'date_limite' => $echeance->date_limite,
            'montant' => $echeance->montant,
            'nb_eleves_en_retard' => $nbEnRetard,
            'est_depassee' => \Carbon\Carbon::parse($echeance->date_limite)->isPast(),
        ];
    });

        return view('admin.dashboard', compact(
    'anneeScolaire', 'totalClasses', 'totalEnseignants',
    'montantAttenduTotal', 'montantEncaisseTotal', 'soldeRestant',
    'elevesEnRetard', 'alertesEcheances'
));
    }
}