<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Recu;
use App\Models\TypeFrais;

class SuiviPaiementController extends Controller
{
    public function index()
    {
        return view('admin.suivi-paiements.index');
    }

    // Recherche d'élèves (nom, prénom ou matricule) ayant une inscription active
    public function rechercher(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $eleves = Eleve::where(function ($query) use ($q) {
                $query->where('nom', 'like', "%{$q}%")
                    ->orWhere('prenom', 'like', "%{$q}%")
                    ->orWhere('matricule', 'like', "%{$q}%");
            })
            ->whereHas('inscriptions', fn($i) => $i->where('statut', 'actif'))
            ->with('inscriptionActuelle.classe')
            ->orderBy('nom')
            ->limit(15)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'nom' => $e->nom,
                'prenom' => $e->prenom,
                'matricule' => $e->matricule,
                'classe' => $e->inscriptionActuelle?->classe?->nom,
            ])
            ->values();

        return response()->json($eleves);
    }

    // Fiche des frais ponctuels (catégorie "autre") d'un élève
    public function fiche($eleveId)
    {
        $eleve = Eleve::findOrFail($eleveId);

        $inscription = Inscription::where('eleve_id', $eleve->id)
            ->where('statut', 'actif')
            ->with('classe')
            ->latest('id')
            ->first();

        if (!$inscription || !$inscription->classe) {
            return response()->json(['message' => "Cet élève n'a pas d'inscription active."], 404);
        }

        $niveau = $inscription->classe->niveau;

        $types = TypeFrais::where('categorie', 'autre')
            ->with('fraisNiveaux')
            ->orderBy('libelle')
            ->get();

        $frais = [];
        $totalDu = 0;
        $totalPaye = 0;

        foreach ($types as $type) {
            $montant = $type->montantPour($niveau);

            if ($montant === null) {
                continue; // frais non défini pour ce niveau
            }

            $paiement = Paiement::where('eleve_id', $eleve->id)
                ->where('type_frais_id', $type->id)
                ->where('inscription_id', $inscription->id)
                ->first();

            $totalDu += $montant;
            if ($paiement) {
                $totalPaye += $paiement->montant_paye;
            }

            $frais[] = [
                'type_frais_id' => $type->id,
                'libelle' => $type->libelle,
                'montant' => $montant,
                'paye' => (bool) $paiement,
                'date_paiement' => $paiement?->date_paiement,
                'recu_id' => $paiement?->recu_id,
            ];
        }

        return response()->json([
            'eleve' => [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'matricule' => $eleve->matricule,
                'classe' => $inscription->classe->nom,
                'niveau' => $niveau,
            ],
            'frais' => $frais,
            'total_du' => $totalDu,
            'total_paye' => $totalPaye,
            'reste' => $totalDu - $totalPaye,
        ]);
    }

    // Encaisse un frais ponctuel (une seule tranche) et génère le reçu
    public function encaisser(Request $request)
    {
        $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'type_frais_id' => 'required|exists:types_frais,id',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|in:especes,mobile_money',
            'observation' => 'nullable|string|max:255',
        ]);

        $type = TypeFrais::with('fraisNiveaux')->findOrFail($request->type_frais_id);

        if ($type->categorie !== 'autre') {
            return back()->withErrors(['type_frais_id' => 'Ce frais ne se paie pas depuis cette page.']);
        }

        $inscription = Inscription::where('eleve_id', $request->eleve_id)
            ->where('statut', 'actif')
            ->with('classe')
            ->latest('id')
            ->first();

        if (!$inscription || !$inscription->classe) {
            return back()->withErrors(['eleve_id' => "Cet élève n'a pas d'inscription active."]);
        }

        $montant = $type->montantPour($inscription->classe->niveau);

        if ($montant === null) {
            return back()->withErrors(['type_frais_id' => "Ce frais n'a pas de montant défini pour ce niveau."]);
        }

        $dejaPaye = Paiement::where('eleve_id', $request->eleve_id)
            ->where('type_frais_id', $type->id)
            ->where('inscription_id', $inscription->id)
            ->exists();

        if ($dejaPaye) {
            return back()->withErrors(['type_frais_id' => 'Ce frais est déjà payé pour cet élève.']);
        }

        $recu = DB::transaction(function () use ($request, $inscription, $type, $montant) {
            $recu = Recu::create([
                'inscription_id' => $inscription->id,
                'secretaire_id' => session('utilisateur_id'),
                'numero_recu' => 'RECU-' . date('Y') . '-' . str_pad(Recu::count() + 1, 5, '0', STR_PAD_LEFT),
                'date_emission' => $request->date_paiement,
            ]);

            Paiement::create([
                'eleve_id' => $request->eleve_id,
                'type_frais_id' => $type->id,
                'comptable_id' => session('utilisateur_id'),
                'recu_id' => $recu->id,
                'inscription_id' => $inscription->id,
                'montant_paye' => $montant,
                'date_paiement' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'observation' => $request->observation,
            ]);

            return $recu;
        });

        return redirect()->route('admin.recus.show', $recu->id)
            ->with('success', 'Paiement enregistré avec succès !');
    }
}