<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paiement;
use App\Models\Eleve;
use App\Models\TypeFrais;
use App\Models\Inscription;
use App\Models\Classe;
use App\Models\Recu;

class PaiementController extends Controller
{
    public function index()
    {
        $paiements = Paiement::with(['eleve', 'typeFrais', 'comptable'])
            ->orderBy('date_paiement', 'desc')
            ->get();
        return view('admin.paiements.index', compact('paiements'));
    }

    public function create()
{
    $classes = Classe::orderBy('niveau')->orderBy('nom')->get();
    $typesFrais = TypeFrais::with('fraisNiveaux')->orderBy('libelle')->get()->map(function ($t) {
        return [
            'id' => $t->id,
            'libelle' => $t->libelle,
            'varie_par_niveau' => $t->varie_par_niveau,
            'montant' => $t->montant,
            'montants_par_niveau' => $t->fraisNiveaux->pluck('montant', 'niveau'),
        ];
    });

    return view('admin.paiements.create', compact('classes', 'typesFrais'));
}

public function elevesParClasse($classeId)
{
    $inscriptions = Inscription::where('classe_id', $classeId)
        ->where('statut', 'actif')
        ->with('eleve')
        ->get()
        ->filter(fn($i) => $i->eleve)
        ->sortBy(fn($i) => $i->eleve->nom);

    $eleves = $inscriptions->map(function ($i) {
        return [
            'id' => $i->eleve->id,
            'nom' => $i->eleve->nom,
            'prenom' => $i->eleve->prenom,
            'matricule' => $i->eleve->matricule,
        ];
    })->values();

    return response()->json($eleves);
}

private function calculerSoldes($eleveId, $classeId)
{
    $classe = Classe::find($classeId);
    if (!$classe) {
        return [];
    }

    $niveau = $classe->niveau;
    $anneeScolaire = $classe->annee_scolaire;

    $estPremiereInscription = Inscription::where('eleve_id', $eleveId)
        ->where('annee_scolaire', '<', $anneeScolaire)
        ->doesntExist();

    $categorieInscription = $estPremiereInscription ? 'inscription' : 'reinscription';

    $typesAVerifier = TypeFrais::whereIn('categorie', [$categorieInscription, 'scolarite'])
        ->with('fraisNiveaux')
        ->get();

    $resultat = [];

    foreach ($typesAVerifier as $type) {
        $montantDu = $type->montantPour($niveau) ?? 0;

        $montantPaye = Paiement::where('eleve_id', $eleveId)
            ->where('type_frais_id', $type->id)
            ->whereYear('date_paiement', '>=', substr($anneeScolaire, 0, 4))
            ->sum('montant_paye');

        $resultat[] = [
            'type_frais_id' => $type->id,
            'libelle' => $type->libelle,
            'montant_du' => $montantDu,
            'montant_paye' => $montantPaye,
            'solde' => max(0, $montantDu - $montantPaye),
        ];
    }

    return $resultat;
}

public function soldeEleve($eleveId, $classeId)
{
    return response()->json($this->calculerSoldes($eleveId, $classeId));
}

    public function store(Request $request)
{
    $request->validate([
        'eleve_id' => 'required|exists:eleves,id',
        'classe_id' => 'required|exists:classes,id',
        'type_frais_id' => 'required|exists:types_frais,id',
        'montant_paye' => 'required|numeric|min:0',
        'date_paiement' => 'required|date',
        'mode_paiement' => 'required|string',
        'observation' => 'nullable|string|max:255',
    ]);

    $soldes = $this->calculerSoldes($request->eleve_id, $request->classe_id);
    $soldeConcerne = collect($soldes)->firstWhere('type_frais_id', (int) $request->type_frais_id);

    if ($soldeConcerne && $request->montant_paye > $soldeConcerne['solde']) {
        return back()->withErrors([
            'montant_paye' => 'Le montant dépasse le solde restant (' . number_format($soldeConcerne['solde'], 0, ',', ' ') . ' FCFA) pour ce frais.',
        ])->withInput();
    }

    $inscription = Inscription::where('eleve_id', $request->eleve_id)
        ->where('classe_id', $request->classe_id)
        ->where('statut', 'actif')
        ->first();

    $recu = Recu::create([
        'inscription_id' => $inscription->id ?? null,
        'secretaire_id' => session('utilisateur_id'),
        'numero_recu' => 'RECU-' . date('Y') . '-' . str_pad(Recu::count() + 1, 5, '0', STR_PAD_LEFT),
        'date_emission' => $request->date_paiement,
    ]);

    Paiement::create([
        'eleve_id' => $request->eleve_id,
        'type_frais_id' => $request->type_frais_id,
        'comptable_id' => session('utilisateur_id'),
        'recu_id' => $recu->id,
        'inscription_id' => $inscription->id ?? null,
        'montant_paye' => $request->montant_paye,
        'date_paiement' => $request->date_paiement,
        'mode_paiement' => $request->mode_paiement,
        'observation' => $request->observation,
    ]);

    return redirect()->route('admin.recus.show', $recu->id)->with('success', 'Paiement enregistré avec succès !');
}

    public function edit($id)
    {
        $paiement   = Paiement::findOrFail($id);
        $eleves     = Eleve::orderBy('nom')->get();
        $typesFrais = TypeFrais::orderBy('libelle')->get();
        return view('admin.paiements.edit', compact('paiement', 'eleves', 'typesFrais'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'eleve_id'       => 'required|exists:eleves,id',
            'type_frais_id'  => 'required|exists:types_frais,id',
            'montant_paye'   => 'required|numeric|min:0',
            'date_paiement'  => 'required|date',
            'mode_paiement'  => 'required|string',
            'observation'    => 'nullable|string|max:255',
        ]);

        Paiement::findOrFail($id)->update($request->only([
            'eleve_id', 'type_frais_id', 'montant_paye',
            'date_paiement', 'mode_paiement', 'observation',
        ]));

        return redirect('/admin/paiements')->with('success', 'Paiement modifié avec succès !');
    }

    public function destroy($id)
    {
        Paiement::findOrFail($id)->delete();
        return redirect('/admin/paiements')->with('success', 'Paiement supprimé !');
    }
}