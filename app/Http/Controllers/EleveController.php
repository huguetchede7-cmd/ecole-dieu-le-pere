<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Support\Facades\Storage;

class EleveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Eleve::with ('classe');

        // Recherche par nom ou prénom
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par classe
        if (request('classe_id')) {
            $query->where('classe_id', request('classe_id'));
        }

        $eleves = $query->orderBy('nom')
                        ->orderBy('prenom')
                        ->paginate(15);

        $classes = Classe::orderBy('niveau')
                         ->orderBy('nom')
                         ->get();

        return view('admin.eleves.index', compact('eleves', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = Classe::orderBy('niveau')
                         ->orderBy('nom')
                         ->get();

        return view('admin.eleves.create', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(Request $request)
{
    $request->validate([
        'nom'            => 'required|string|max:100',
        'prenom'         => 'required|string|max:100',
        'date_naissance' => 'required|date',
        'sexe'           => 'required|in:M,F',
        'nom_parent'     => 'required|string|max:200',
        'contact_parent' => 'required|string|max:20',
        'adresse'        => 'nullable|string',
        'photo'          => 'nullable|image|max:2048',
        'classe_id'      => 'required|exists:classes,id',
        'annee_scolaire' => 'required|string',
    ]);

    $data = $request->only(['nom', 'prenom', 'date_naissance', 'sexe', 'nom_parent', 'contact_parent', 'adresse']);

    if ($request->hasFile('photo')) {
        $data['photo'] = $request->file('photo')->store('photos/eleves', 'public');
    }

    $eleve = Eleve::create($data);

    // Créer l'inscription
    \App\Models\Inscription::create([
        'eleve_id'       => $eleve->id,
        'classe_id'      => $request->classe_id,
        'annee_scolaire' => $request->annee_scolaire,
        'date_inscription' => now(),
        'statut'         => 'actif'
    ]);

    return redirect('/admin/eleves')->with('success', 'Élève ajouté avec succès !');
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $eleve = Eleve::with('classe')->findOrFail($id);
        return view('admin.eleves.show', compact('eleve'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $eleve = Eleve::findOrFail($id);
        $classes = Classe::orderBy('niveau')
                         ->orderBy('nom')
                         ->get();

        return view('admin.eleves.edit', compact('eleve', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'date_naissance' => 'required|date',
            'sexe'           => 'required|in:M,F',
            'classe_id'      => 'required|exists:classes,id',
            'nom_parent'     => 'required|string|max:200',
            'contact_parent' => 'required|string|max:20',
            'adresse'        => 'nullable|string',
            'annee_scolaire' => 'required|string',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $eleve = Eleve::findOrFail($id);
        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            // Suppression de l'ancienne photo
            if ($eleve->photo) {
                Storage::disk('public')->delete($eleve->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos/eleves', 'public');
        }

        $eleve->update($data);

        return redirect()->route('admin.eleves.index')
                         ->with('success', 'Élève modifié avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $eleve = Eleve::findOrFail($id);

        // Suppression de la photo si elle existe
        if ($eleve->photo) {
            Storage::disk('public')->delete($eleve->photo);
        }

        $eleve->delete();

        return redirect()->route('admin.eleves.index')
                         ->with('success', 'Élève supprimé avec succès !');
    }
}