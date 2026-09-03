<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matiere;
use App\Models\Classe;

class MatiereController extends Controller
{
    private array $niveaux = [
        'Maternelle 1', 'Maternelle 2', 'CI',
        'CP', 'CE1', 'CE2', 'CM1', 'CM2',
    ];

    public function index()
    {
        $data = [];
        foreach ($this->niveaux as $niveau) {
            $data[] = [
                'niveau' => $niveau,
                'count' => Matiere::where('niveau', $niveau)->count(),
                'hasClasse' => Classe::where('niveau', $niveau)->exists(),
            ];
        }
        return view('admin.matieres.index', ['data' => $data]);
    }

    public function niveau($niveau)
    {
        $classes = Classe::where('niveau', $niveau)->orderBy('nom')->get();
        $matieres = Matiere::where('niveau', $niveau)
            ->orderBy('nom')
            ->get();

        return view('admin.matieres.niveau', [
            'niveau' => $niveau,
            'classes' => $classes,
            'matieres' => $matieres,
        ]);
    }

    public function create(Request $request)
    {
        $selectedNiveau = $request->query('niveau');
        return view('admin.matieres.create', [
            'niveaux' => $this->niveaux,
            'selectedNiveau' => $selectedNiveau,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'niveau' => 'required|in:' . implode(',', $this->niveaux),
        ]);

        $matiere = Matiere::create($request->only(['nom', 'niveau']));

        return redirect()->route('admin.matieres.niveau', $matiere->niveau)
            ->with('success', 'Matière créée avec succès !');
    }

    public function edit($id)
    {
        $matiere = Matiere::findOrFail($id);
        return view('admin.matieres.edit', [
            'matiere' => $matiere,
            'niveaux' => $this->niveaux,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'niveau' => 'required|in:' . implode(',', $this->niveaux),
        ]);

        $matiere = Matiere::findOrFail($id);
        $matiere->update($request->only(['nom', 'niveau']));

        return redirect()->route('admin.matieres.niveau', $matiere->niveau)
            ->with('success', 'Matière modifiée avec succès !');
    }

    public function destroy($id)
    {
        $matiere = Matiere::findOrFail($id);
        $niveau = $matiere->niveau;
        $matiere->delete();

        return redirect()->route('admin.matieres.niveau', $niveau)
            ->with('success', 'Matière supprimée !');
    }
}