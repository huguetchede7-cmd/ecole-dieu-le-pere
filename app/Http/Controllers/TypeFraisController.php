<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeFrais;

class TypeFraisController extends Controller
{
    public function index()
    {
        $typesFrais = TypeFrais::orderBy('libelle')->get();
        return view('admin.types_frais.index', compact('typesFrais'));
    }

    public function create()
    {
        return view('admin.types_frais.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'libelle'     => 'required|string|max:100',
            'montant'     => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        TypeFrais::create($request->all());

        return redirect('/admin/types-frais')->with('success', 'Type de frais créé avec succès !');
    }

    public function edit($id)
    {
        $typeFrais = TypeFrais::findOrFail($id);
        return view('admin.types_frais.edit', compact('typeFrais'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'libelle'     => 'required|string|max:100',
            'montant'     => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        TypeFrais::findOrFail($id)->update($request->all());

        return redirect('/admin/types-frais')->with('success', 'Type de frais modifié avec succès !');
    }

    public function destroy($id)
    {
        TypeFrais::findOrFail($id)->delete();
        return redirect('/admin/types-frais')->with('success', 'Type de frais supprimé !');
    }
}