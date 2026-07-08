<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matiere;
use App\Models\Classe;

class MatiereController extends Controller
{
    public function index()
    {
        $matieres = Matiere::with('classe')->orderBy('nom')->get();
        return view('admin.matieres.index', compact('matieres'));
    }

    public function create()
    {
        $classes = Classe::orderBy('nom')->get();
        return view('admin.matieres.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:100',
            'classe_id' => 'required|exists:classes,id',
        ]);

        Matiere::create($request->all());

        return redirect('/admin/matieres')->with('success', 'Matière créée avec succès !');
    }

    public function edit($id)
    {
        $matiere = Matiere::findOrFail($id);
        $classes = Classe::orderBy('nom')->get();
        return view('admin.matieres.edit', compact('matiere', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom'       => 'required|string|max:100',
            'classe_id' => 'required|exists:classes,id',
        ]);

        Matiere::findOrFail($id)->update($request->all());

        return redirect('/admin/matieres')->with('success', 'Matière modifiée avec succès !');
    }

    public function destroy($id)
    {
        Matiere::findOrFail($id)->delete();
        return redirect('/admin/matieres')->with('success', 'Matière supprimée !');
    }
}