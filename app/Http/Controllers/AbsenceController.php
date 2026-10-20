<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Inscription;

class AbsenceController extends Controller
{
    private function estEnseignant(): bool
    {
        return session('utilisateur_role') === 'enseignant';
    }

    // Classe de l'enseignant connecté (null s'il n'en a pas)
    private function classeEnseignant()
    {
        return Classe::where('enseignant_id', session('utilisateur_id'))->first();
    }

    // Classes que l'utilisateur a le droit de voir
    private function classesAutorisees()
    {
        if ($this->estEnseignant()) {
            return Classe::where('enseignant_id', session('utilisateur_id'))->get();
        }
        return Classe::orderBy('niveau')->orderBy('nom')->get();
    }

    // Refuse l'accès si l'enseignant demande une autre classe que la sienne
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

    public function index(Request $request)
    {
        $classes = $this->classesAutorisees();

        $classeId = $request->query('classe_id');
        $date = $request->query('date');

        // Un enseignant arrive directement sur sa classe, à la date du jour
        if ($this->estEnseignant()) {
            $classeId = optional($classes->first())->id;
            $date = $date ?: date('Y-m-d');
        }

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
        $classes = $this->classesAutorisees();
        return view('admin.absences.create', compact('classes'));
    }

    public function elevesAvecAbsences($classeId, $date)
    {
        $this->verifierClasse($classeId);

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
            $this->verifierEleve($ligne['eleve_id']);
        }

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
        $this->verifierEleve($absence->eleve_id);

        $autorises = $this->elevesAutorises();
        $eleves = Eleve::when($autorises !== null, fn($q) => $q->whereIn('id', $autorises))
            ->orderBy('nom')
            ->get();

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

        $absence = Absence::findOrFail($id);
        $this->verifierEleve($absence->eleve_id);
        $this->verifierEleve($request->eleve_id);

        $absence->update($request->only([
            'eleve_id', 'date', 'statut', 'motif',
        ]));

        return redirect('/admin/absences')->with('success', 'Enregistrement modifié avec succès !');
    }

    public function destroy($id)
    {
        $absence = Absence::findOrFail($id);
        $this->verifierEleve($absence->eleve_id);
        $absence->delete();

        return redirect('/admin/absences')->with('success', 'Enregistrement supprimé !');
    }
}