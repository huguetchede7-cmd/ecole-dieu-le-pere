<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Classe;
use App\Models\Epreuve;
use App\Models\Examen;
use App\Models\Matiere;
use App\Models\Parametre;

class EpreuveController extends Controller
{
    // La classe de l'enseignant connecté (une seule)
    private function classeDeLEnseignant()
    {
        return Classe::where('enseignant_id', session('utilisateur_id'))->first();
    }

    // Page "Mes épreuves" : les sessions en cours et les matières de la classe
    public function index()
    {
        $classe = $this->classeDeLEnseignant();
        $annee = Parametre::anneeCourante();

        // Sessions de l'année qui ne sont pas terminées
        $examens = Examen::where('annee_scolaire', $annee)
            ->orderBy('date_debut')
            ->get()
            ->filter(fn($e) => $e->phase()[0] !== 'Terminé');

        // Matières du niveau de la classe
        $matieres = $classe
            ? Matiere::where('niveau', $classe->niveau)->orderBy('nom')->get()
            : collect();

        // Épreuves déjà déposées, retrouvables par "session-matière"
        $epreuves = $classe
            ? Epreuve::where('classe_id', $classe->id)->get()
                ->keyBy(fn($e) => $e->examen_id . '-' . $e->matiere_id)
            : collect();

        return view('admin.epreuves.index', compact('classe', 'examens', 'matieres', 'epreuves'));
    }

    // Enregistrer le fichier envoyé par l'enseignant
    public function store(Request $request)
    {
        $classe = $this->classeDeLEnseignant();
        abort_unless($classe, 403, "Aucune classe ne vous est affectée.");

        $request->validate([
            'examen_id'  => 'required|exists:examens,id',
            'matiere_id' => 'required|exists:matieres,id',
            'fichier'    => 'required|file|mimes:pdf,doc,docx|max:10240',
        ], [
            'fichier.required' => 'Choisissez le fichier de l\'épreuve.',
            'fichier.mimes'    => 'Le fichier doit être un PDF ou un document Word (.doc, .docx).',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        $examen = Examen::findOrFail($request->examen_id);
        $matiere = Matiere::findOrFail($request->matiere_id);

        // L'enseignant ne dépose que pour les matières de sa classe
        abort_unless($matiere->niveau === $classe->niveau, 403, "Cette matière n'appartient pas à votre classe.");

        // Après la date limite : on accepte quand même, mais on note le retard
        $enRetard = now('Africa/Porto-Novo')->toDateString() > $examen->date_limite_depot->toDateString();

        // Si une épreuve existe déjà, on supprime l'ancien fichier (le nouveau le remplace)
        $existante = Epreuve::where('examen_id', $examen->id)
            ->where('classe_id', $classe->id)
            ->where('matiere_id', $matiere->id)
            ->first();

        if ($existante) {
            Storage::disk('local')->delete($existante->fichier);
        }

        // Le fichier est rangé dans un dossier privé (non accessible depuis l'extérieur)
        $fichier = $request->file('fichier');
        $chemin = $fichier->store('epreuves', 'local');

        Epreuve::updateOrCreate(
            [
                'examen_id'  => $examen->id,
                'classe_id'  => $classe->id,
                'matiere_id' => $matiere->id,
            ],
            [
                'fichier'          => $chemin,
                'nom_original'     => $fichier->getClientOriginalName(),
                'depose_par'       => session('utilisateur_id'),
                'depose_en_retard' => $enRetard,
            ]
        );

        $message = 'Épreuve de ' . $matiere->nom . ' déposée avec succès !';
        if ($enRetard) {
            $message .= ' (dépôt après la date limite : le retard est noté)';
        }

        return redirect('/admin/epreuves')->with('success', $message);
    }

        // Suivi des épreuves : qui a déposé, qui n'a pas encore déposé (admin, directeur, secrétaire)
    public function suivi(Request $request)
    {
        $annee = Parametre::anneeCourante();

        $examens = Examen::where('annee_scolaire', $annee)
            ->orderByDesc('date_debut')
            ->get();

        // Session affichée : celle choisie, sinon la première non terminée, sinon la plus récente
        $examen = $request->query('examen_id')
            ? $examens->firstWhere('id', (int) $request->query('examen_id'))
            : ($examens->first(fn($e) => $e->phase()[0] !== 'Terminé') ?? $examens->first());

        // Classes de l'année, rangées de Maternelle 1 à CM2
        $ordre = ['Maternelle 1', 'Maternelle 2', 'CI', 'CP', 'CE1', 'CE2', 'CM1', 'CM2'];

        $classes = Classe::where('annee_scolaire', $annee)
            ->with('enseignant')
            ->get()
            ->sortBy(function ($c) use ($ordre) {
                $rang = array_search($c->niveau, $ordre);
                return sprintf('%02d-%s', $rang === false ? 99 : $rang, $c->nom);
            })
            ->values();

        // Matières regroupées par niveau
        $matieresParNiveau = Matiere::orderBy('nom')->get()->groupBy('niveau');

        // Épreuves déjà déposées pour cette session, retrouvables par "classe-matière"
        $epreuves = $examen
            ? Epreuve::where('examen_id', $examen->id)->with('auteur')->get()
                ->keyBy(fn($e) => $e->classe_id . '-' . $e->matiere_id)
            : collect();

        return view('admin.epreuves.suivi', compact('examens', 'examen', 'classes', 'matieresParNiveau', 'epreuves'));
    }

    // Télécharger une épreuve (l'enseignant ne voit que celles de sa classe)
    public function telecharger($id)
    {
        $epreuve = Epreuve::findOrFail($id);

        if (session('utilisateur_role') === 'enseignant') {
            $classe = $this->classeDeLEnseignant();
            abort_unless($classe && $classe->id === $epreuve->classe_id, 403, 'Accès non autorisé.');
        }

        abort_unless(Storage::disk('local')->exists($epreuve->fichier), 404, 'Fichier introuvable.');

        return Storage::disk('local')->download($epreuve->fichier, $epreuve->nom_original);
    }
}