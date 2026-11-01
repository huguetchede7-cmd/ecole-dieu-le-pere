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
    // Formats acceptés : Word, PDF, images (photo d'un schéma dessiné à la main)
    private const REGLE_FICHIER = 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240';

    private function messagesFichier(): array
    {
        return [
            'fichier.required' => 'Choisissez le fichier de l\'épreuve.',
            'fichier.mimes'    => 'Le fichier doit être un PDF, un document Word (.doc, .docx) ou une image (.jpg, .png).',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 10 Mo.',
        ];
    }

    // La classe de l'enseignant connecté (une seule)
    private function classeDeLEnseignant()
    {
        return Classe::where('enseignant_id', session('utilisateur_id'))->first();
    }

    // Enregistre le fichier d'une épreuve (une épreuve par session, niveau et matière). Retourne true si dépôt en retard.
    private function enregistrer(Examen $examen, string $niveau, Matiere $matiere, $fichier): bool
    {
        // Après la date limite : on accepte quand même, mais on note le retard
        $enRetard = now('Africa/Porto-Novo')->toDateString() > $examen->date_limite_depot->toDateString();

        // Si une épreuve existe déjà, on supprime l'ancien fichier (le nouveau le remplace)
        $existante = Epreuve::where('examen_id', $examen->id)
            ->where('niveau', $niveau)
            ->where('matiere_id', $matiere->id)
            ->first();

        if ($existante) {
            Storage::disk('local')->delete($existante->fichier);
        }

        // Le fichier est rangé dans un dossier privé (non accessible depuis l'extérieur)
        $chemin = $fichier->store('epreuves', 'local');

        Epreuve::updateOrCreate(
            [
                'examen_id'  => $examen->id,
                'niveau'     => $niveau,
                'matiere_id' => $matiere->id,
            ],
            [
                'fichier'          => $chemin,
                'nom_original'     => $fichier->getClientOriginalName(),
                'depose_par'       => session('utilisateur_id'),
                'depose_en_retard' => $enRetard,
            ]
        );

        return $enRetard;
    }

    // Page "Mes épreuves" : les sessions en cours et les matières du niveau
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

        // Épreuves du niveau (communes à toutes les classes du niveau), retrouvables par "session-matière"
        $epreuves = $classe
            ? Epreuve::where('niveau', $classe->niveau)->with('auteur')->get()
                ->keyBy(fn($e) => $e->examen_id . '-' . $e->matiere_id)
            : collect();

        // Les autres classes du même niveau (CI B pour CI A) : elles partagent la même épreuve
        $autresClasses = $classe
            ? Classe::where('annee_scolaire', $classe->annee_scolaire)
                ->where('niveau', $classe->niveau)
                ->where('id', '!=', $classe->id)
                ->with('enseignant')
                ->get()
            : collect();

        return view('admin.epreuves.index', compact('classe', 'examens', 'matieres', 'epreuves', 'autresClasses'));
    }

    // Enregistrer le fichier envoyé par l'enseignant
    public function store(Request $request)
    {
        $classe = $this->classeDeLEnseignant();
        abort_unless($classe, 403, "Aucune classe ne vous est affectée.");

        $request->validate([
            'examen_id'  => 'required|exists:examens,id',
            'matiere_id' => 'required|exists:matieres,id',
            'fichier'    => self::REGLE_FICHIER,
        ], $this->messagesFichier());

        $examen = Examen::findOrFail($request->examen_id);
        $matiere = Matiere::findOrFail($request->matiere_id);

        // L'enseignant ne dépose que pour les matières de son niveau
        abort_unless($matiere->niveau === $classe->niveau, 403, "Cette matière n'appartient pas à votre classe.");

        $enRetard = $this->enregistrer($examen, $classe->niveau, $matiere, $request->file('fichier'));

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

        // Niveaux rangés de Maternelle 1 à CM2, chacun avec ses classes (CI A, CI B...)
        $ordre = ['Maternelle 1', 'Maternelle 2', 'CI', 'CP', 'CE1', 'CE2', 'CM1', 'CM2'];

        $groupes = Classe::where('annee_scolaire', $annee)
            ->with('enseignant')
            ->get()
            ->sortBy('nom')
            ->groupBy('niveau')
            ->sortBy(function ($classes, $niveau) use ($ordre) {
                $rang = array_search($niveau, $ordre);
                return $rang === false ? 99 : $rang;
            });

        // Matières regroupées par niveau
        $matieresParNiveau = Matiere::orderBy('nom')->get()->groupBy('niveau');

        // Épreuves déjà déposées pour cette session, retrouvables par "niveau|matière"
        $epreuves = $examen
            ? Epreuve::where('examen_id', $examen->id)->with('auteur')->get()
                ->keyBy(fn($e) => $e->niveau . '|' . $e->matiere_id)
            : collect();

        return view('admin.epreuves.suivi', compact('examens', 'examen', 'groupes', 'matieresParNiveau', 'epreuves'));
    }

    // Le directeur (ou l'admin) dépose une épreuve à la place des enseignants, en dernier recours
    public function deposerPourEnseignant(Request $request)
    {
        $request->validate([
            'examen_id'  => 'required|exists:examens,id',
            'niveau'     => 'required|string|max:30',
            'matiere_id' => 'required|exists:matieres,id',
            'fichier'    => self::REGLE_FICHIER,
        ], $this->messagesFichier());

        $examen = Examen::findOrFail($request->examen_id);
        $matiere = Matiere::findOrFail($request->matiere_id);

        // La matière doit appartenir au niveau choisi
        abort_unless($matiere->niveau === $request->niveau, 403, "Cette matière n'appartient pas à ce niveau.");

        // depose_par = le directeur : on saura que ce ne sont pas les enseignants qui l'ont déposée
        $this->enregistrer($examen, $request->niveau, $matiere, $request->file('fichier'));

        return redirect('/admin/suivi-epreuves?examen_id=' . $examen->id)
            ->with('success', 'Épreuve de ' . $matiere->nom . ' déposée pour le niveau ' . $request->niveau . ' à la place des enseignants.');
    }

    // Ouvrir les épreuves avant la date prévue (directeur ou admin) : l'action est enregistrée
    public function ouvrirAnticipe($id)
    {
        $examen = Examen::findOrFail($id);

        if (!$examen->epreuvesOuvertes()) {
            $examen->update([
                'ouverture_anticipee_par' => session('utilisateur_id'),
                'ouverture_anticipee_le'  => now(),
            ]);
        }

        return redirect('/admin/suivi-epreuves?examen_id=' . $examen->id)
            ->with('success', 'Les épreuves sont maintenant ouvertes.');
    }

    // Lire une épreuve : l'enseignant lit celles de son niveau ; la direction et le secrétariat seulement après l'ouverture
    public function telecharger($id)
    {
        $epreuve = Epreuve::with('examen')->findOrFail($id);

        if (session('utilisateur_role') === 'enseignant') {
            $classe = $this->classeDeLEnseignant();
            abort_unless($classe && $classe->niveau === $epreuve->niveau, 403, 'Accès non autorisé.');
        } else {
            abort_unless(
                $epreuve->examen->epreuvesOuvertes(),
                403,
                'Cette épreuve est verrouillée jusqu\'au ' . $epreuve->examen->dateOuverture()->format('d/m/Y') . '.'
            );
        }

        abort_unless(Storage::disk('local')->exists($epreuve->fichier), 404, 'Fichier introuvable.');

        return Storage::disk('local')->download($epreuve->fichier, $epreuve->nom_original);
    }
}