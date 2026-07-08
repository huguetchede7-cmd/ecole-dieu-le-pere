<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plainte;
use App\Models\Eleve;

class PlainteController extends Controller
{
    public function index()
    {
        $plaintes = Plainte::with(['eleve', 'secretaire'])
            ->orderBy('date_plainte', 'desc')
            ->get();
        return view('admin.plaintes.index', compact('plaintes'));
    }

    public function create()
    {
        $eleves = Eleve::orderBy('nom')->get();
        return view('admin.plaintes.create', compact('eleves'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'eleve_id'     => 'required|exists:eleves,id',
            'description'  => 'required|string',
            'date_plainte' => 'required|date',
        ]);

        Plainte::create([
            'eleve_id'      => $request->eleve_id,
            'secretaire_id' => session('utilisateur_id'),
            'description'   => $request->description,
            'statut'        => 'en_cours',
            'date_plainte'  => $request->date_plainte,
        ]);

        return redirect('/admin/plaintes')->with('success', 'Plainte enregistrée avec succès !');
    }

    public function edit($id)
    {
        $plainte = Plainte::findOrFail($id);
        $eleves  = Eleve::orderBy('nom')->get();
        return view('admin.plaintes.edit', compact('plainte', 'eleves'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'statut'  => 'required|in:en_cours,resolue,rejetee',
            'reponse' => 'nullable|string',
        ]);

        Plainte::findOrFail($id)->update($request->only(['statut', 'reponse']));

        return redirect('/admin/plaintes')->with('success', 'Plainte mise à jour avec succès !');
    }

    public function destroy($id)
    {
        Plainte::findOrFail($id)->delete();
        return redirect('/admin/plaintes')->with('success', 'Plainte supprimée !');
    }
}