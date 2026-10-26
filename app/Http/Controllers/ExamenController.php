<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Examen;
use App\Models\Parametre;

class ExamenController extends Controller
{
    private function regles(): array
    {
        return [
            'titre'             => 'required|string|max:150',
            'type'              => 'required|in:Composition,Devoir,Examen blanc',
            'periode'           => 'required|in:1er trimestre,2eme trimestre,3eme trimestre',
            'annee_scolaire'    => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'date_limite_depot' => 'required|date',
            'date_debut'        => 'required|date|after_or_equal:date_limite_depot',
            'date_fin'          => 'required|date|after_or_equal:date_debut',
            'date_limite_notes' => 'required|date|after_or_equal:date_fin',
            'consignes'         => 'nullable|string|max:1000',
        ];
    }

    private function messages(): array
    {
        return [
            'annee_scolaire.regex'            => "L'année scolaire doit être au format 2025-2026.",
            'date_debut.after_or_equal'       => "La composition ne peut pas commencer avant la date limite de dépôt des épreuves.",
            'date_fin.after_or_equal'         => "La fin de la composition ne peut pas précéder son début.",
            'date_limite_notes.after_or_equal'=> "La date limite de saisie des notes doit être après la fin de la composition.",
        ];
    }

    public function index(Request $request)
    {
        $annee = $request->query('annee_scolaire', Parametre::anneeCourante());

        $examens = Examen::where('annee_scolaire', $annee)
            ->orderByDesc('date_debut')
            ->get();

        return view('admin.examens.index', compact('examens', 'annee'));
    }

    public function create()
    {
        return view('admin.examens.form', ['examen' => null]);
    }

    public function store(Request $request)
    {
        $donnees = $request->validate($this->regles(), $this->messages());
        $donnees['cree_par'] = session('utilisateur_id');

        Examen::create($donnees);

        return redirect('/admin/examens')->with('success', 'Session créée avec succès !');
    }

    public function edit($id)
    {
        $examen = Examen::findOrFail($id);
        return view('admin.examens.form', compact('examen'));
    }

    public function update(Request $request, $id)
    {
        $examen = Examen::findOrFail($id);
        $examen->update($request->validate($this->regles(), $this->messages()));

        return redirect('/admin/examens')->with('success', 'Session modifiée avec succès !');
    }

    public function destroy($id)
    {
        Examen::findOrFail($id)->delete();
        return redirect('/admin/examens')->with('success', 'Session supprimée !');
    }
}
