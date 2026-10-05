<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeFrais;
use App\Models\FraisNiveau;

class TypeFraisController extends Controller
{
    private array $niveaux = [
    'Maternelle 1', 'Maternelle 2', 'CI',
    'CP', 'CE1', 'CE2', 'CM1', 'CM2',
];
    public function index()
    {
        $typesFrais = TypeFrais::orderBy('libelle')->get();
        return view('admin.types_frais.index', compact('typesFrais'));
    }

    public function create()
{
    return view('admin.types_frais.create', ['niveaux' => $this->niveaux]);
}

    public function store(Request $request)
{
    $request->validate([
        'libelle' => 'required|string|max:100',
        'varie_par_niveau' => 'nullable|boolean',
        'montant' => 'required_if:varie_par_niveau,0|nullable|numeric|min:0',
        'description' => 'nullable|string|max:255',
        'montants_niveaux' => 'nullable|array',
        'montants_niveaux.*' => 'nullable|numeric|min:0',
        'categorie' => 'required|in:inscription,reinscription,scolarite,autre',
    ]);

    $varie = $request->boolean('varie_par_niveau');

    $typeFrais = TypeFrais::create([
        'libelle' => $request->libelle,
        'description' => $request->description,
        'varie_par_niveau' => $varie,
        'montant' => $varie ? null : $request->montant,
        'categorie' => $request->categorie,
    ]);

    if ($varie) {
        foreach ($request->input('montants_niveaux', []) as $niveau => $montant) {
            if ($montant !== null && $montant !== '') {
                FraisNiveau::create([
                    'type_frais_id' => $typeFrais->id,
                    'niveau' => $niveau,
                    'montant' => $montant,
                ]);
            }
        }
    }

    return redirect('/admin/types-frais')->with('success', 'Type de frais créé avec succès !');
}

    public function edit($id)
{
    $typeFrais = TypeFrais::with('fraisNiveaux')->findOrFail($id);
    return view('admin.types_frais.edit', ['typeFrais' => $typeFrais, 'niveaux' => $this->niveaux]);
}

    public function update(Request $request, $id)
{
    $request->validate([
        'libelle' => 'required|string|max:100',
        'varie_par_niveau' => 'nullable|boolean',
        'montant' => 'required_if:varie_par_niveau,0|nullable|numeric|min:0',
        'description' => 'nullable|string|max:255',
        'montants_niveaux' => 'nullable|array',
        'montants_niveaux.*' => 'nullable|numeric|min:0',
        'categorie' => 'required|in:inscription,reinscription,scolarite,autre',
    ]);

    $typeFrais = TypeFrais::findOrFail($id);
    $varie = $request->boolean('varie_par_niveau');

    $typeFrais->update([
        'libelle' => $request->libelle,
        'description' => $request->description,
        'varie_par_niveau' => $varie,
        'montant' => $varie ? null : $request->montant,
        'categorie' => $request->categorie,
    ]);

    $typeFrais->fraisNiveaux()->delete();

    if ($varie) {
        foreach ($request->input('montants_niveaux', []) as $niveau => $montant) {
            if ($montant !== null && $montant !== '') {
                FraisNiveau::create([
                    'type_frais_id' => $typeFrais->id,
                    'niveau' => $niveau,
                    'montant' => $montant,
                ]);
            }
        }
    }

    return redirect('/admin/types-frais')->with('success', 'Type de frais modifié avec succès !');
}

    public function destroy($id)
    {
        TypeFrais::findOrFail($id)->delete();
        return redirect('/admin/types-frais')->with('success', 'Type de frais supprimé !');
    }
}