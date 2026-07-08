<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Matiere;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::with(['eleve', 'matiere', 'enseignant'])
            ->orderBy('annee_scolaire', 'desc')
            ->get();
        return view('admin.notes.index', compact('notes'));
    }

    public function create()
    {
        $eleves   = Eleve::orderBy('nom')->get();
        $matieres = Matiere::orderBy('nom')->get();
        return view('admin.notes.create', compact('eleves', 'matieres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'eleve_id'       => 'required|exists:eleves,id',
            'matiere_id'     => 'required|exists:matieres,id',
            'note'           => 'required|numeric|min:0',
            'note_max'       => 'required|numeric|min:1',
            'periode'        => 'required|in:1er trimestre,2eme trimestre,3eme trimestre',
            'annee_scolaire' => 'required|string',
            'observation'    => 'nullable|string|max:255',
        ]);

        Note::create([
            'eleve_id'       => $request->eleve_id,
            'matiere_id'     => $request->matiere_id,
            'enseignant_id'  => session('utilisateur_id'),
            'note'           => $request->note,
            'note_max'       => $request->note_max,
            'periode'        => $request->periode,
            'annee_scolaire' => $request->annee_scolaire,
            'observation'    => $request->observation,
        ]);

        return redirect('/admin/notes')->with('success', 'Note enregistrée avec succès !');
    }

    public function edit($id)
    {
        $note     = Note::findOrFail($id);
        $eleves   = Eleve::orderBy('nom')->get();
        $matieres = Matiere::orderBy('nom')->get();
        return view('admin.notes.edit', compact('note', 'eleves', 'matieres'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'eleve_id'       => 'required|exists:eleves,id',
            'matiere_id'     => 'required|exists:matieres,id',
            'note'           => 'required|numeric|min:0',
            'note_max'       => 'required|numeric|min:1',
            'periode'        => 'required|in:1er trimestre,2eme trimestre,3eme trimestre',
            'annee_scolaire' => 'required|string',
            'observation'    => 'nullable|string|max:255',
        ]);

        Note::findOrFail($id)->update($request->only([
            'eleve_id', 'matiere_id', 'note', 'note_max',
            'periode', 'annee_scolaire', 'observation',
        ]));

        return redirect('/admin/notes')->with('success', 'Note modifiée avec succès !');
    }

    public function destroy($id)
    {
        Note::findOrFail($id)->delete();
        return redirect('/admin/notes')->with('success', 'Note supprimée !');
    }
}