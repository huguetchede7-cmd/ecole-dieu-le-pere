<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Inscription;
use App\Models\Classe;
use App\Models\NoteCorrection;

class NoteController extends Controller
{
    // ---------- Contrôle d'accès par classe (enseignant) ----------

    private function estEnseignant(): bool
    {
        return session('utilisateur_role') === 'enseignant';
    }

    private function classeEnseignant()
    {
        return Classe::where('enseignant_id', session('utilisateur_id'))->first();
    }

    private function classesAutorisees()
    {
        if ($this->estEnseignant()) {
            return Classe::where('enseignant_id', session('utilisateur_id'))->get();
        }
        return Classe::orderBy('niveau')->orderBy('nom')->get();
    }

    private function verifierClasse($classeId): void
    {
        if (!$this->estEnseignant()) return;

        $classe = $this->classeEnseignant();
        abort_unless($classe && $classe->id == $classeId, 403, "Cette classe n'est pas la vôtre.");
    }

    // IDs des élèves autorisés (null = tous, pour les autres rôles)
    private function elevesAutorises(): ?array
    {
        if (!$this->estEnseignant()) return null;

        $classe = $this->classeEnseignant();
        if (!$classe) return [];

        return Inscription::where('classe_id', $classe->id)
            ->where('statut', 'actif')
            ->pluck('eleve_id')
            ->all();
    }

    private function verifierEleve($eleveId): void
    {
        $autorises = $this->elevesAutorises();
        abort_unless($autorises === null || in_array($eleveId, $autorises), 403, "Cet élève n'est pas dans votre classe.");
    }

    // ---------- Historique des corrections ----------

    // Garde une trace si la note ou le barème a changé
    private function enregistrerCorrection(Note $note, $nouvelleNote, $nouvelleMax, $motif = null): void
    {
        if ((float) $note->note === (float) $nouvelleNote && (float) $note->note_max === (float) $nouvelleMax) {
            return;
        }

        NoteCorrection::create([
            'note_id'           => $note->id,
            'ancienne_note'     => $note->note,
            'ancienne_note_max' => $note->note_max,
            'nouvelle_note'     => $nouvelleNote,
            'nouvelle_note_max' => $nouvelleMax,
            'motif'             => $motif,
            'modifie_par'       => session('utilisateur_id'),
        ]);
    }

    // ---------- Pages ----------

    public function index()
    {
        $autorises = $this->elevesAutorises();

        $eleves = Note::with('eleve')
            ->when($autorises !== null, fn($q) => $q->whereIn('eleve_id', $autorises))
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

        // Corrections récentes : pour l'enseignant, celles faites par d'autres sur SES notes
        $moi = session('utilisateur_id');
        $corrections = NoteCorrection::with(['note.eleve', 'note.matiere', 'auteur'])
            ->when($this->estEnseignant(), function ($q) use ($moi) {
                $q->whereHas('note', fn($n) => $n->where('enseignant_id', $moi))
                  ->where(fn($c) => $c->whereNull('modifie_par')->orWhere('modifie_par', '!=', $moi));
            })
            ->latest()
            ->limit(20)
            ->get()
            ->filter(fn($c) => $c->note && $c->note->eleve);

        return view('admin.notes.index', compact('eleves', 'corrections'));
    }

    // Toutes les notes d'un élève pour une année, avec l'historique des corrections
    public function notesEleve($eleveId, $annee)
    {
        $this->verifierEleve($eleveId);

        $eleve = Eleve::findOrFail($eleveId);

        $notes = Note::with(['matiere', 'enseignant', 'corrections.auteur'])
            ->where('eleve_id', $eleveId)
            ->where('annee_scolaire', $annee)
            ->get()
            ->sortBy(fn($n) => ($n->matiere->nom ?? '') . $n->periode)
            ->values();

        return view('admin.notes.eleve', compact('eleve', 'notes', 'annee'));
    }

    public function create()
    {
        $classes = $this->classesAutorisees();
        $matieres = Matiere::orderBy('nom')->get();
        return view('admin.notes.create', compact('classes', 'matieres'));
    }

    public function elevesParClasse($classeId)
    {
        $this->verifierClasse($classeId);

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
        $this->verifierClasse($classeId);

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
            $this->verifierEleve($ligne['eleve_id']);
        }

        foreach ($request->notes as $ligne) {
            if (!isset($ligne['note']) || $ligne['note'] === '' || $ligne['note'] === null) {
                continue;
            }

            $noteMax = $ligne['note_max'] ?? 20;

            $note = Note::firstOrNew([
                'eleve_id' => $ligne['eleve_id'],
                'matiere_id' => $request->matiere_id,
                'periode' => $request->periode,
                'annee_scolaire' => $request->annee_scolaire,
            ]);

            if ($note->exists) {
                // Note déjà saisie : on garde l'enseignant d'origine et on trace le changement
                $this->enregistrerCorrection($note, $ligne['note'], $noteMax);
            } else {
                $note->enseignant_id = session('utilisateur_id');
            }

            $note->note = $ligne['note'];
            $note->note_max = $noteMax;
            $note->save();
        }

        return redirect('/admin/notes')->with('success', 'Notes enregistrées avec succès !');
    }

    public function edit($id)
    {
        $note = Note::with(['eleve', 'matiere'])->findOrFail($id);
        $this->verifierEleve($note->eleve_id);

        return view('admin.notes.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'note'        => 'required|numeric|min:0',
            'note_max'    => 'required|numeric|min:1',
            'observation' => 'nullable|string|max:255',
            'motif'       => 'nullable|string|max:255',
        ]);

        $note = Note::findOrFail($id);
        $this->verifierEleve($note->eleve_id);

        $this->enregistrerCorrection($note, $request->note, $request->note_max, $request->motif);

        // Seule la note, le barème et l'observation sont modifiables
        $note->update($request->only(['note', 'note_max', 'observation']));

        return redirect()->route('admin.notes.eleve', [
            'eleve' => $note->eleve_id,
            'annee_scolaire' => $note->annee_scolaire,
        ])->with('success', 'Note corrigée avec succès !');
    }

    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        $this->verifierEleve($note->eleve_id);
        $note->delete();

        return redirect('/admin/notes')->with('success', 'Note supprimée !');
    }

    public function bulletin($eleveId, $anneeScolaire)
    {
        $this->verifierEleve($eleveId);

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
