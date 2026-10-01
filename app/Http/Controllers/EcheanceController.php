<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Echeance;

class EcheanceController extends Controller
{
    public function index()
    {
        $echeances = Echeance::orderBy('date_limite')->get();
        return view('admin.echeances.index', compact('echeances'));
    }

    public function create()
    {
        return view('admin.echeances.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:100',
            'annee_scolaire' => 'required|string',
            'date_limite' => 'required|date',
            'montant' => 'required|numeric|min:0',
        ]);

        Echeance::create($request->all());

        return redirect('/admin/echeances')->with('success', 'Échéance créée avec succès !');
    }

    public function edit($id)
    {
        $echeance = Echeance::findOrFail($id);
        return view('admin.echeances.edit', compact('echeance'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'libelle' => 'required|string|max:100',
            'annee_scolaire' => 'required|string',
            'date_limite' => 'required|date',
            'montant' => 'required|numeric|min:0',
        ]);

        Echeance::findOrFail($id)->update($request->all());

        return redirect('/admin/echeances')->with('success', 'Échéance modifiée avec succès !');
    }

    public function destroy($id)
    {
        Echeance::findOrFail($id)->delete();
        return redirect('/admin/echeances')->with('success', 'Échéance supprimée !');
    }
}