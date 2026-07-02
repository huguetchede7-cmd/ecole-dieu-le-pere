<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classe;

class ClasseController extends Controller
{
    public function index()
    {
        $classes = Classe::orderBy('niveau')->orderBy('nom')->get();
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('admin.classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'           => 'required|string|max:100',
            'niveau'        => 'required|string',
            'capacite'      => 'required|integer|min:1',
            'annee_scolaire'=> 'required|string',
        ]);

        Classe::create($request->all());

        return redirect('/admin/classes')->with('success', 'Classe créée avec succès !');
    }

    public function edit($id)
    {
        $classe = Classe::findOrFail($id);
        return view('admin.classes.edit', compact('classe'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom'           => 'required|string|max:100',
            'niveau'        => 'required|string',
            'capacite'      => 'required|integer|min:1',
            'annee_scolaire'=> 'required|string',
        ]);

        Classe::findOrFail($id)->update($request->all());

        return redirect('/admin/classes')->with('success', 'Classe modifiée avec succès !');
    }

    public function destroy($id)
    {
        Classe::findOrFail($id)->delete();
        return redirect('/admin/classes')->with('success', 'Classe supprimée !');
    }
}