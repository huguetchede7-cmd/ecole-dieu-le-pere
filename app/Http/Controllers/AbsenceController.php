<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Inscription;

class AbsenceController extends Controller
{
    public function index(Request $request)
{
    $classes = Classe::orderBy('niveau')->orderBy('nom')->get();

    $classeId = $request->query('classe_id');
    $date = $request->query('date');

    $resultats = collect();

    if ($classeId && $date) {
        $inscriptions = Inscription::where('classe_id', $classeId)
            ->where('statut', 'actif')
            ->with('eleve')
            ->get()
            ->filter(fn($i) => $i->eleve)
            ->sortBy(fn($i) => $i->eleve->nom);

        $absencesExistantes = Absence::where('date', $date)
            ->whereIn('eleve_id', $inscriptions->pluck('eleve.id'))
            ->with('enseignant')
            ->get()
            ->keyBy('eleve_id');

        $resultats = $inscriptions->map(function ($i) use ($absencesExistantes) {
            $a = $absencesExistantes->get($i->eleve->id);

            return [
                'nom' => $i->eleve->nom,
                'prenom' => $i->eleve->prenom,
                'statut' => $a->statut ?? null,
                'motif' => $a->motif ?? null,
                'enseignant' => $a->enseignant->nom ?? null,
            ];
        })->values();
    }

    return view('admin.absences.index', compact('classes', 'classeId', 'date', 'resultats'));
}

    public function create()
{
    $classes = Classe::orderBy('niveau')->orderBy('nom')->get();
    return view('admin.absences.create', compact('classes'));
}

public function elevesAvecAbsences($classeId, $date)
{
    $inscriptions = Inscription::where('classe_id', $classeId)
        ->where('statut', 'actif')
        ->with('eleve')
        ->get()
        ->filter(fn($i) => $i->eleve)
        ->sortBy(fn($i) => $i->eleve->nom);

    $absencesExistantes = Absence::where('date', $date)
        ->whereIn('eleve_id', $inscriptions->pluck('eleve.id'))
        ->get()
        ->keyBy('eleve_id');

    $resultat = $inscriptions->map(function ($i) use ($absencesExistantes) {
        $absenceExistante = $absencesExistantes->get($i->eleve->id);

        return [
            'eleve_id' => $i->eleve->id,
            'nom' => $i->eleve->nom,
            'prenom' => $i->eleve->prenom,
            'statut' => $absenceExistante->statut ?? 'present',
            'motif' => $absenceExistante->motif ?? '',
        ];
    })->values();

    return response()->json($resultat);
}

    public function store(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'absences' => 'required|array',
        'absences.*.eleve_id' => 'required|exists:eleves,id',
        'absences.*.statut' => 'required|in:present,absent,retard',
        'absences.*.motif' => 'nullable|string|max:255',
    ]);

    foreach ($request->absences as $ligne) {
        Absence::updateOrCreate(
            [
                'eleve_id' => $ligne['eleve_id'],
                'date' => $request->date,
            ],
            [
                'enseignant_id' => session('utilisateur_id'),
                'statut' => $ligne['statut'],
                'motif' => $ligne['motif'] ?? null,
            ]
        );
    }

    return redirect('/admin/absences')->with('success', 'Absences enregistrées avec succès !');
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