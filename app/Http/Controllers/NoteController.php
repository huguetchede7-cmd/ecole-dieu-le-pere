<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Inscription;
use App\Models\Classe;
use App\Models\NoteCorrection;
use App\Models\Utilisateur;

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

    // ---------- Corrections de notes (avec validation par l'enseignant) ----------

    // Nombre maximum de corrections appliquées pour une même note
    private const MAX_CORRECTIONS = 2;

    // Qui doit valider une correction : l'enseignant qui a saisi la note,
    // sinon l'enseignant actuel de la classe de l'élève (note saisie par l'admin)
    private function validateurPour(Note $note): ?int
    {
        $proprietaire = $note->enseignant_id ? Utilisateur::find($note->enseignant_id) : null;
        if ($proprietaire && $proprietaire->role === 'enseignant') {
            return $proprietaire->id;
        }

        $inscription = Inscription::where('eleve_id', $note->eleve_id)
            ->where('annee_scolaire', $note->annee_scolaire)
            ->with('classe')
            ->first();

        return $inscription->classe->enseignant_id ?? null;
    }

    // Modifie une note (enseignant : direct) ou propose une correction (directeur/admin : validation requise).
    // Retourne ['ok' => bool, 'applique' => bool, 'message' => string]
    private function modifierNote(Note $note, $nouvelleNote, $nouvelleMax, $motif = null): array
    {
        if ((float) $note->note === (float) $nouvelleNote && (float) $note->note_max === (float) $nouvelleMax) {
            return ['ok' => true, 'applique' => true, 'message' => 'Aucun changement de note.'];
        }

        $appliquees = NoteCorrection::where('note_id', $note->id)->where('statut', 'validee')->count();
        if ($appliquees >= self::MAX_CORRECTIONS) {
            return [
                'ok' => false,
                'applique' => false,
                'message' => 'Cette note a déjà été corrigée ' . self::MAX_CORRECTIONS . ' fois : plus aucune modification n\'est possible.',
            ];
        }

        if (NoteCorrection::where('note_id', $note->id)->where('statut', 'en_attente')->exists()) {
            return [
                'ok' => false,
                'applique' => false,
                'message' => 'Une correction est déjà en attente de validation pour cette note.',
            ];
        }

        $moi = session('utilisateur_id');
        $validateur = $this->estEnseignant() ? $moi : $this->validateurPour($note);

        $donnees = [
            'note_id'           => $note->id,
            'ancienne_note'     => $note->note,
            'ancienne_note_max' => $note->note_max,
            'nouvelle_note'     => $nouvelleNote,
            'nouvelle_note_max' => $nouvelleMax,
            'motif'             => $motif,
            'modifie_par'       => $moi,
        ];

        // L'enseignant modifie directement ; sans enseignant à consulter, la correction est aussi directe
        if ($this->estEnseignant() || $validateur === null) {
            NoteCorrection::create($donnees + [
                'statut'        => 'validee',
                'validateur_id' => $validateur ?? $moi,
                'decide_le'     => now(),
            ]);
            $note->update(['note' => $nouvelleNote, 'note_max' => $nouvelleMax]);

            return ['ok' => true, 'applique' => true, 'message' => 'Note corrigée avec succès !'];
        }

        // Directeur / admin : la note ne change pas tant que l'enseignant n'a pas confirmé
        NoteCorrection::create($donnees + [
            'statut'        => 'en_attente',
            'validateur_id' => $validateur,
        ]);

        return [
            'ok' => true,
            'applique' => false,
            'message' => "Correction envoyée à l'enseignant pour validation. La note actuelle reste inchangée jusqu'à sa confirmation.",
        ];
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

        // Corrections en attente (pour l'enseignant : celles qu'il doit valider)
        $nbEnAttente = NoteCorrection::where('statut', 'en_attente')
            ->when($this->estEnseignant(), fn($q) => $q->where('validateur_id', session('utilisateur_id')))
            ->count();

        return view('admin.notes.index', compact('eleves', 'nbEnAttente'));
    }

        // Page des corrections : à valider (enseignant) ou suivi (directeur, admin)
    public function corrections()
    {
        $moi = session('utilisateur_id');

        $base = NoteCorrection::with(['note.eleve', 'note.matiere', 'auteur', 'validateur'])
            ->when($this->estEnseignant(), function ($q) use ($moi) {
                $q->where('validateur_id', $moi)
                  ->where(fn($c) => $c->whereNull('modifie_par')->orWhere('modifie_par', '!=', $moi));
            });

        $enAttente = (clone $base)->where('statut', 'en_attente')->oldest()->get()
            ->filter(fn($c) => $c->note && $c->note->eleve);

        $traitees = (clone $base)->whereIn('statut', ['validee', 'refusee'])->latest()->limit(30)->get()
            ->filter(fn($c) => $c->note && $c->note->eleve);

        return view('admin.notes.corrections', compact('enAttente', 'traitees'));
    }

    // Seul l'enseignant désigné peut traiter une correction en attente
    private function correctionPourValidation($id): NoteCorrection
    {
        $correction = NoteCorrection::with('note')->findOrFail($id);

        abort_unless(
            $this->estEnseignant()
                && $correction->validateur_id == session('utilisateur_id')
                && $correction->statut === 'en_attente'
                && $correction->note,
            403,
            'Vous ne pouvez pas traiter cette correction.'
        );

        return $correction;
    }

    public function confirmerCorrection($id)
    {
        $correction = $this->correctionPourValidation($id);
        $note = $correction->note;

        $appliquees = NoteCorrection::where('note_id', $note->id)->where('statut', 'validee')->count();
        if ($appliquees >= self::MAX_CORRECTIONS) {
            return back()->with('erreur', 'Cette note a déjà été corrigée ' . self::MAX_CORRECTIONS . ' fois : confirmation impossible.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($note, $correction) {
            // La nouvelle note devient la note officielle et est attribuée à l'enseignant qui confirme
            $note->update([
                'note'          => $correction->nouvelle_note,
                'note_max'      => $correction->nouvelle_note_max,
                'enseignant_id' => session('utilisateur_id'),
            ]);

            $correction->update([
                'statut'    => 'validee',
                'decide_le' => now(),
            ]);
        });

        return redirect('/admin/notes/corrections')
            ->with('success', 'Correction confirmée : la nouvelle note est maintenant la note officielle.');
    }

        // Le directeur / l'admin marque une réponse comme consultée
    public function marquerVue($id)
    {
        $correction = NoteCorrection::findOrFail($id);
        abort_unless($correction->modifie_par == session('utilisateur_id'), 403);

        $correction->update(['vue_par_auteur_le' => now()]);

        return back();
    }

    public function marquerToutesVues()
    {
        NoteCorrection::where('modifie_par', session('utilisateur_id'))
            ->whereIn('statut', ['validee', 'refusee'])
            ->whereNull('vue_par_auteur_le')
            ->update(['vue_par_auteur_le' => now()]);

        return back();
    }

    public function refuserCorrection(Request $request, $id)
    {
        $request->validate(
            ['motif_refus' => 'required|string|max:255'],
            ['motif_refus.required' => 'Indiquez la raison du refus.']
        );

        $correction = $this->correctionPourValidation($id);

        $correction->update([
            'statut'      => 'refusee',
            'decide_le'   => now(),
            'motif_refus' => $request->motif_refus,
        ]);

        return redirect('/admin/notes/corrections')
            ->with('success', 'Correction refusée : la note reste inchangée.');
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

        $problemes = [];

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

            // Nouvelle note : enregistrement simple
            if (!$note->exists) {
                $note->enseignant_id = session('utilisateur_id');
                $note->note = $ligne['note'];
                $note->note_max = $noteMax;
                $note->save();
                continue;
            }

            // Note déjà saisie : toute modification est encadrée
            $nomEleve = optional(Eleve::find($ligne['eleve_id']))->nom;

            if ($this->estEnseignant()) {
                $res = $this->modifierNote($note, $ligne['note'], $noteMax);
                if (!$res['ok']) {
                    $problemes[] = $nomEleve . ' : ' . $res['message'];
                }
            } elseif ((float) $note->note !== (float) $ligne['note'] || (float) $note->note_max !== (float) $noteMax) {
                $problemes[] = $nomEleve . " : note existante non modifiée (utilisez « Corriger », l'enseignant devra valider).";
            }
        }

        $redirection = redirect('/admin/notes')->with('success', 'Notes enregistrées avec succès !');
        if ($problemes) {
            $redirection->with('erreur', implode(' ', $problemes));
        }

        return $redirection;
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
            // Le directeur et l'admin doivent justifier leur proposition
            'motif'       => ($this->estEnseignant() ? 'nullable' : 'required') . '|string|max:255',
        ], [
            'motif.required' => 'Le motif de la correction est obligatoire.',
        ]);

        $note = Note::findOrFail($id);
        $this->verifierEleve($note->eleve_id);

        $res = $this->modifierNote($note, $request->note, $request->note_max, $request->motif);

        if (!$res['ok']) {
            return back()->withErrors(['note' => $res['message']])->withInput();
        }

        if ($res['applique']) {
            $note->update(['observation' => $request->observation]);
        }

        return redirect()->route('admin.notes.eleve', [
            'eleve' => $note->eleve_id,
            'annee_scolaire' => $note->annee_scolaire,
        ])->with('success', $res['message']);
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
