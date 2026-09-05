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
    $eleves = Note::with('eleve')
        ->get()
        ->groupBy('eleve_id')
        ->map(function ($notes) {
            $eleve = $notes->first()->eleve;
            return [
                'id' => $eleve->id ?? null,
                'nom' => $eleve->nom ?? 'N/A',
                'prenom' => $eleve->prenom ?? '',
                'matricule' => $eleve->matricule ?? 'N/A',
                'nb_notes' => $notes->count(),
                'derniere_annee' => $notes->sortByDesc('annee_scolaire')->first()->annee_scolaire,
            ];
        })
        ->filter(fn($e) => $e['id'] !== null)
        ->sortBy('nom')
        ->values();

    return view('admin.notes.index', compact('eleves'));
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

    $inscription = \App\Models\Inscription::where('eleve_id', $eleveId)
        ->where('annee_scolaire', $anneeScolaire)
        ->with('classe')
        ->first();

    $niveau = $inscription->classe->niveau ?? null;

    $periodes = ['1er trimestre', '2eme trimestre', '3eme trimestre'];

    $toutesNotes = Note::with('matiere')
        ->where('eleve_id', $eleveId)
        ->where('annee_scolaire', $anneeScolaire)
        ->get();

    if ($niveau) {
        $nomsMatieres = Matiere::where('niveau', $niveau)->orderBy('nom')->pluck('nom', 'id');
    } else {
        $nomsMatieres = $toutesNotes->pluck('matiere.nom', 'matiere_id')->unique();
    }

    $tableau = [];
    foreach ($nomsMatieres as $matiereId => $nom) {
        $tableau[$matiereId] = ['nom' => $nom];
        foreach ($periodes as $periode) {
            $tableau[$matiereId][$periode] = null;
        }
    }

    foreach ($toutesNotes->groupBy('periode') as $periode => $notesPeriode) {
        foreach ($notesPeriode->groupBy('matiere_id') as $matiereId => $notesMatiere) {
            $moyenne = round($notesMatiere->avg(function ($n) {
                return ($n->note / $n->note_max) * 20;
            }), 2);

            if (!isset($tableau[$matiereId])) {
                $tableau[$matiereId] = ['nom' => $notesMatiere->first()->matiere->nom ?? 'N/A'];
                foreach ($periodes as $p) {
                    $tableau[$matiereId][$p] = null;
                }
            }

            $tableau[$matiereId][$periode] = $moyenne;
        }
    }

    // Fonction d'appréciation selon la moyenne
    $appreciation = function ($moyenne) {
        if ($moyenne === null) return null;
        if ($moyenne >= 16) return 'Excellent';
        if ($moyenne >= 14) return 'Très Bien';
        if ($moyenne >= 12) return 'Bien';
        if ($moyenne >= 10) return 'Assez-Bien';
        return 'Insuffisant';
    };

    $moyennesTrimestrielles = [];
    $appreciationsTrimestrielles = [];

    foreach ($periodes as $periode) {
        $valeurs = collect($tableau)->pluck($periode)->filter(fn($v) => $v !== null);
        $moy = $valeurs->count() > 0 ? round($valeurs->avg(), 2) : null;
        $moyennesTrimestrielles[$periode] = $moy;
        $appreciationsTrimestrielles[$periode] = $appreciation($moy);
    }

    $moyenneAnnuelle = null;
    $appreciationAnnuelle = null;
    $decision = null;

    if (collect($moyennesTrimestrielles)->filter()->count() === 3) {
        $moyenneAnnuelle = round(array_sum($moyennesTrimestrielles) / 3, 2);
        $appreciationAnnuelle = $appreciation($moyenneAnnuelle);
        $decision = $moyenneAnnuelle >= 10 ? 'admis' : 'refuse';

        \App\Models\Inscription::where('eleve_id', $eleveId)
            ->where('annee_scolaire', $anneeScolaire)
            ->update(['decision' => $decision]);
    }

    $classeNom = $inscription->classe->nom ?? 'N/A';

    return view('admin.notes.bulletin', compact(
        'eleve', 'anneeScolaire', 'periodes', 'tableau',
        'moyennesTrimestrielles', 'appreciationsTrimestrielles',
        'moyenneAnnuelle', 'appreciationAnnuelle', 'decision', 'classeNom'
    ));
}
}