<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscription;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\TypeFrais;
use App\Models\Recu;
use App\Models\Paiement;

class InscriptionController extends Controller
{
    public function index()
    {
        $inscriptions = Inscription::with(['eleve', 'classe', 'recu.paiements'])
            ->orderBy('annee_scolaire', 'desc')
            ->orderBy('date_inscription', 'desc')
            ->get();

        return view('admin.inscriptions.index', compact('inscriptions'));
    }

    public function rechercherEleve($matricule)
    {
        $eleve = Eleve::where('matricule', $matricule)->first();

        if (!$eleve) {
            return response()->json(['trouve' => false]);
        }

        $derniereInscription = Inscription::where('eleve_id', $eleve->id)
            ->orderBy('annee_scolaire', 'desc')
            ->with('classe')
            ->first();

        return response()->json([
            'trouve' => true,
            'eleve' => [
                'id' => $eleve->id,
                'nom' => $eleve->nom,
                'prenom' => $eleve->prenom,
                'matricule' => $eleve->matricule,
            ],
            'derniere_inscription' => $derniereInscription ? [
                'classe' => $derniereInscription->classe->nom ?? null,
                'niveau' => $derniereInscription->classe->niveau ?? null,
                'annee_scolaire' => $derniereInscription->annee_scolaire,
                'decision' => $derniereInscription->decision,
            ] : null,
        ]);
    }

    public function create()
    {
        $eleves     = Eleve::orderBy('nom')->get();
        $classes    = Classe::orderBy('niveau')->orderBy('nom')->get();
        $typesFrais = TypeFrais::orderBy('libelle')->get();
        return view('admin.inscriptions.create', compact('eleves', 'classes', 'typesFrais'));
    }

    public function store(Request $request)
    {
        // On retire les lignes de paiement vides (ajoutées puis non remplies)
        $paiementsInput = collect($request->input('paiements', []))
            ->filter(fn($p) => !empty($p['montant_paye']) && !empty($p['type_frais_id']))
            ->values()
            ->all();
        $request->merge(['paiements' => $paiementsInput]);

        $request->validate([

'mode' => 'required|in:nouveau,reinscription',

'nom' => 'required_if:mode,nouveau|nullable|string|max:100',

'prenom' => 'required_if:mode,nouveau|nullable|string|max:100',

'date_naissance' => 'required_if:mode,nouveau|nullable|date',

'lieu_naissance' => 'nullable|string|max:150',

'sexe' => 'required_if:mode,nouveau|nullable|in:M,F',

'nom_parent' => 'nullable|string|max:150',

'contact_parent' => 'nullable|string|max:50',

'eleve_id' => 'required_if:mode,reinscription|nullable|exists:eleves,id',

'classe_id' => 'required|exists:classes,id',

'annee_scolaire' => 'required|string',

'date_inscription' => 'required|date',

'statut' => 'required|in:actif,inactif',

'paiements' => 'nullable|array',

'paiements.*.type_frais_id' => 'required|exists:types_frais,id',

'paiements.*.montant_paye' => 'required|numeric|min:0',

'paiements.*.mode_paiement' => 'required|string',

]);


if ($request->mode === 'reinscription') {

$eleve = Eleve::findOrFail($request->eleve_id);

} else {

$eleve = Eleve::create([

'matricule' => Eleve::genererMatricule(),

'nom' => $request->nom,

'prenom' => $request->prenom,

'date_naissance' => $request->date_naissance,

'lieu_naissance' => $request->lieu_naissance,

'sexe' => $request->sexe,

'nom_parent' => $request->nom_parent,

'contact_parent' => $request->contact_parent,

]);

}

        if ($request->statut === 'actif') {
        Inscription::where('eleve_id', $eleve->id)
        ->where('statut', 'actif')
        ->update(['statut' => 'inactif']);
        }

        $inscription = Inscription::create([
        'eleve_id' => $eleve->id,
        'classe_id' => $request->classe_id,
        'annee_scolaire' => $request->annee_scolaire,
        'date_inscription' => $request->date_inscription,
        'statut' => $request->statut,
]);

        // On génère toujours un reçu pour l'inscription, même sans paiement
        $recu = Recu::create([
            'inscription_id' => $inscription->id,
            'secretaire_id'  => session('utilisateur_id'),
            'numero_recu'    => 'RECU-' . date('Y') . '-' . str_pad(Recu::count() + 1, 5, '0', STR_PAD_LEFT),
            'date_emission'  => $request->date_inscription,
        ]);

        foreach ($paiementsInput as $ligne) {
            Paiement::create([
                'eleve_id' => $eleve->id,
                'type_frais_id'  => $ligne['type_frais_id'],
                'comptable_id'   => session('utilisateur_id'),
                'recu_id'        => $recu->id,
                'inscription_id' => $inscription->id,
                'montant_paye'   => $ligne['montant_paye'],
                'date_paiement'  => $request->date_inscription,
                'mode_paiement'  => $ligne['mode_paiement'],
                'observation'    => 'Paiement effectué à l\'inscription',
            ]);
        }

        return redirect()->route('admin.recus.show', $recu->id)
            ->with('success', 'Inscription enregistrée avec succès !');
    }

    public function edit($id)
    {
        $inscription = Inscription::findOrFail($id);
        $eleves      = Eleve::orderBy('nom')->get();
        $classes     = Classe::orderBy('niveau')->orderBy('nom')->get();
        return view('admin.inscriptions.edit', compact('inscription', 'eleves', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
'eleve_id' => 'required|exists:eleves,id',
'classe_id' => 'required|exists:classes,id',
'annee_scolaire' => 'required|string',
'date_inscription' => 'required|date',
'statut' => 'required|in:actif,inactif',
'decision' => 'nullable|in:admis,refuse',
]);

        $inscription = Inscription::findOrFail($id);

        if ($request->statut === 'actif') {
            Inscription::where('eleve_id', $request->eleve_id)
                ->where('id', '!=', $id)
                ->where('statut', 'actif')
                ->update(['statut' => 'inactif']);
        }

        $inscription->update($request->only([
'eleve_id', 'classe_id', 'annee_scolaire', 'date_inscription', 'statut', 'decision',
]));

        return redirect()->route('admin.inscriptions.index')
            ->with('success', 'Inscription modifiée avec succès !');
    }

    public function destroy($id)
    {
        Inscription::findOrFail($id)->delete();
        return redirect()->route('admin.inscriptions.index')
            ->with('success', 'Inscription supprimée !');
    }
}