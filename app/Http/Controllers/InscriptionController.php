<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscription;
use App\Models\Eleve;
use App\Models\Classe;

class InscriptionController extends Controller
{
    public function index()
    {
        $inscriptions = Inscription::with(['eleve', 'classe'])
            ->orderBy('annee_scolaire', 'desc')
            ->get();
        return view('admin.inscriptions.index', compact('inscriptions'));
    }

    public function create()
    {
        $eleves  = Eleve::orderBy('nom')->get();
        $classes = Classe::orderBy('nom')->get();
        return view('admin.inscriptions.create', compact('eleves', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'eleve_id'          => 'required|exists:eleves,id',
            'classe_id'         => 'required|exists:classes,id',
            'annee_scolaire'    => 'required|string',
            'date_inscription'  => 'required|date',
            'statut'            => 'required|in:actif,inactif',
        ]);

        // Si la nouvelle inscription est active, on désactive les anciennes
        // inscriptions actives du même élève (un seul statut "actif" à la fois)
        if ($request->statut === 'actif') {
            Inscription::where('eleve_id', $request->eleve_id)
                ->where('statut', 'actif')
                ->update(['statut' => 'inactif']);
        }

        $inscription = Inscription::create($request->all());

        // Synchronisation : on met à jour la classe actuelle de l'élève
        if ($request->statut === 'actif') {
            Eleve::findOrFail($request->eleve_id)->update([
                'classe_id'      => $request->classe_id,
                'annee_scolaire' => $request->annee_scolaire,
            ]);
        }

        return redirect('/admin/inscriptions')->with('success', 'Inscription enregistrée avec succès !');
    }

    public function edit($id)
    {
        $inscription = Inscription::findOrFail($id);
        $eleves      = Eleve::orderBy('nom')->get();
        $classes     = Classe::orderBy('nom')->get();
        return view('admin.inscriptions.edit', compact('inscription', 'eleves', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'eleve_id'          => 'required|exists:eleves,id',
            'classe_id'         => 'required|exists:classes,id',
            'annee_scolaire'    => 'required|string',
            'date_inscription'  => 'required|date',
            'statut'            => 'required|in:actif,inactif',
        ]);

        $inscription = Inscription::findOrFail($id);

        if ($request->statut === 'actif') {
            Inscription::where('eleve_id', $request->eleve_id)
                ->where('id', '!=', $id)
                ->where('statut', 'actif')
                ->update(['statut' => 'inactif']);
        }

        $inscription->update($request->all());

        if ($request->statut === 'actif') {
            Eleve::findOrFail($request->eleve_id)->update([
                'classe_id'      => $request->classe_id,
                'annee_scolaire' => $request->annee_scolaire,
            ]);
        }

        return redirect('/admin/inscriptions')->with('success', 'Inscription modifiée avec succès !');
    }

    public function destroy($id)
    {
        Inscription::findOrFail($id)->delete();
        return redirect('/admin/inscriptions')->with('success', 'Inscription supprimée !');
    }
}