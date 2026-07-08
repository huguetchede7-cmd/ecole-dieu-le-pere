<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absence;
use App\Models\Eleve;

class AbsenceController extends Controller
{
    public function index()
    {
        $absences = Absence::with(['eleve', 'enseignant'])
            ->orderBy('date', 'desc')
            ->get();
        return view('admin.absences.index', compact('absences'));
    }

    public function create()
    {
        $eleves = Eleve::orderBy('nom')->get();
        return view('admin.absences.create', compact('eleves'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'date'     => 'required|date',
            'statut'   => 'required|in:present,absent,retard',
            'motif'    => 'nullable|string|max:255',
        ]);

        Absence::create([
            'eleve_id'      => $request->eleve_id,
            'enseignant_id' => session('utilisateur_id'),
            'date'          => $request->date,
            'statut'        => $request->statut,
            'motif'         => $request->motif,
        ]);

        return redirect('/admin/absences')->with('success', 'Enregistrement effectué avec succès !');
    }

    public function edit($id)
    {
        $absence = Absence::findOrFail($id);
        $eleves  = Eleve::orderBy('nom')->get();
        return view('admin.absences.edit', compact('absence', 'eleves'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'date'     => 'required|date',
            'statut'   => 'required|in:present,absent,retard',
            'motif'    => 'nullable|string|max:255',
        ]);

        Absence::findOrFail($id)->update($request->only([
            'eleve_id', 'date', 'statut', 'motif',
        ]));

        return redirect('/admin/absences')->with('success', 'Enregistrement modifié avec succès !');
    }

    public function destroy($id)
    {
        Absence::findOrFail($id)->delete();
        return redirect('/admin/absences')->with('success', 'Enregistrement supprimé !');
    }
}