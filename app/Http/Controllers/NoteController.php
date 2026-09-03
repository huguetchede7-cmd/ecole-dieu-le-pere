<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Inscription;
use App\Models\Classe;

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
    $classes = Classe::orderBy('niveau')->orderBy('nom')->get();
    $matieres = Matiere::orderBy('nom')->get();
    return view('admin.notes.create', compact('classes', 'matieres'));
}

public function elevesParClasse($classeId)
{
    $inscriptions = Inscription::where('classe_id', $classeId)
        ->where('statut', 'actif')
        ->with('eleve')
        ->get();

    $eleves = $inscriptions->filter(fn($i) => $i->eleve)->map(function ($i) {
        return [
            'id' => $i->eleve->id,
            'nom' => $i->eleve->nom,
            'prenom' => $i->eleve->prenom,
            'matricule' => $i->eleve->matricule,
        ];
    })->values();

    return response()->json($eleves);
}

public function elevesAvecNotes($classeId, $matiereId, $periode, $annee)
{
    $inscriptions = Inscription::where('classe_id', $classeId)
        ->where('statut', 'actif')
        ->with('eleve')
        ->get()
        ->filter(fn($i) => $i->eleve)
        ->sortBy(fn($i) => $i->eleve->nom);

    $notesExistantes = Note::where('matiere_id', $matiereId)
        ->where('periode', $periode)
        ->where('annee_scolaire', $annee)
        ->get()
        ->keyBy('eleve_id');

    $resultat = $inscriptions->map(function ($i) use ($notesExistantes) {
        $noteExistante = $notesExistantes->get($i->eleve->id);

        return [
            'eleve_id' => $i->eleve->id,
            'nom' => $i->eleve->nom,
            'prenom' => $i->eleve->prenom,
            'note' => $noteExistante->note ?? null,
            'note_max' => $noteExistante->note_max ?? 20,
        ];
    })->values();

    return response()->json($resultat);
}

   public function store(Request $request)
{
    $request->validate([
        'matiere_id' => 'required|exists:matieres,id',
        'periode' => 'required|in:1er trimestre,2eme trimestre,3eme trimestre',
        'annee_scolaire' => 'required|string',
        'notes' => 'required|array',
        'notes.*.eleve_id' => 'required|exists:eleves,id',
        'notes.*.note' => 'nullable|numeric|min:0',
        'notes.*.note_max' => 'nullable|numeric|min:1',
    ]);

    foreach ($request->notes as $ligne) {
        if (!isset($ligne['note']) || $ligne['note'] === '' || $ligne['note'] === null) {
            continue;
        }

        Note::updateOrCreate(
            [
                'eleve_id' => $ligne['eleve_id'],
                'matiere_id' => $request->matiere_id,
                'periode' => $request->periode,
                'annee_scolaire' => $request->annee_scolaire,
            ],
            [
                'enseignant_id' => session('utilisateur_id'),
                'note' => $ligne['note'],
                'note_max' => $ligne['note_max'] ?? 20,
            ]
        );
    }

    return redirect('/admin/notes')->with('success', 'Notes enregistrées avec succès !');
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
    public function bulletin($eleveId, $anneeScolaire)
{
    $eleve = Eleve::findOrFail($eleveId);

    $periodes = ['1er trimestre', '2eme trimestre', '3eme trimestre'];
    $bulletin = [];
    $moyennesGenerales = [];

    foreach ($periodes as $periode) {
        $notes = Note::with('matiere')
            ->where('eleve_id', $eleveId)
            ->where('annee_scolaire', $anneeScolaire)
            ->where('periode', $periode)
            ->get()
            ->groupBy('matiere_id');

        $moyennesParMatiere = [];

        foreach ($notes as $matiereId => $notesMatiere) {
            $moyenneNotes = $notesMatiere->avg(function ($note) {
                return ($note->note / $note->note_max) * 20;
            });

            $moyennesParMatiere[] = [
                'matiere' => $notesMatiere->first()->matiere->nom ?? 'N/A',
                'moyenne' => round($moyenneNotes, 2),
            ];
        }

        $moyenneGeneraleTrimestre = count($moyennesParMatiere) > 0
            ? round(collect($moyennesParMatiere)->avg('moyenne'), 2)
            : null;

        $bulletin[$periode] = [
            'matieres' => $moyennesParMatiere,
            'moyenne_generale' => $moyenneGeneraleTrimestre,
        ];

        if ($moyenneGeneraleTrimestre !== null) {
            $moyennesGenerales[] = $moyenneGeneraleTrimestre;
        }
    }

    $moyenneAnnuelle = null;
    $decision = null;

    if (count($moyennesGenerales) === 3) {
        $moyenneAnnuelle = round(array_sum($moyennesGenerales) / 3, 2);
        $decision = $moyenneAnnuelle >= 10 ? 'admis' : 'refuse';

        \App\Models\Inscription::where('eleve_id', $eleveId)
            ->where('annee_scolaire', $anneeScolaire)
            ->update(['decision' => $decision]);
    }

    return view('admin.notes.bulletin', compact(
        'eleve', 'anneeScolaire', 'bulletin', 'moyenneAnnuelle', 'decision'
    ));
}
}