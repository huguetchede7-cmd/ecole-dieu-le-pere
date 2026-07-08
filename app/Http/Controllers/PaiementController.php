<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paiement;
use App\Models\Eleve;
use App\Models\TypeFrais;

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
        $eleves     = Eleve::orderBy('nom')->get();
        $typesFrais = TypeFrais::orderBy('libelle')->get();
        return view('admin.paiements.create', compact('eleves', 'typesFrais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'eleve_id'       => 'required|exists:eleves,id',
            'type_frais_id'  => 'required|exists:types_frais,id',
            'montant_paye'   => 'required|numeric|min:0',
            'date_paiement'  => 'required|date',
            'mode_paiement'  => 'required|string',
            'observation'    => 'nullable|string|max:255',
        ]);

        Paiement::create([
            'eleve_id'      => $request->eleve_id,
            'type_frais_id' => $request->type_frais_id,
            'comptable_id'  => session('utilisateur_id'),
            'montant_paye'  => $request->montant_paye,
            'date_paiement' => $request->date_paiement,
            'mode_paiement' => $request->mode_paiement,
            'observation'   => $request->observation,
        ]);

        return redirect('/admin/paiements')->with('success', 'Paiement enregistré avec succès !');
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