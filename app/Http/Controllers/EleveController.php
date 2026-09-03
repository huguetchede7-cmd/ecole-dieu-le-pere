<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Inscription;
use Illuminate\Support\Facades\Storage;

class EleveController extends Controller
{
    public function index()
    {
        $query = Eleve::with('inscriptionActuelle.classe');

        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%");
            });
        }

        if (request('classe_id')) {
            $query->whereHas('inscriptionActuelle', function($q) {
                $q->where('classe_id', request('classe_id'));
            });
        }

        $eleves = $query->orderBy('nom')->orderBy('prenom')->paginate(15);

        $classes = Classe::orderBy('niveau')->orderBy('nom')->get();

        return view('admin.eleves.index', compact('eleves', 'classes'));
    }

    public function create()
    {
        $classes = Classe::orderBy('niveau')->orderBy('nom')->get();
        return view('admin.eleves.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'nom_parent' => 'required|string|max:200',
            'contact_parent' => 'required|string|max:20',
            'adresse' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'classe_id' => 'required|exists:classes,id',
            'annee_scolaire' => 'required|string',
        ]);

        $data = $request->only(['nom', 'prenom', 'date_naissance', 'sexe', 'nom_parent', 'contact_parent', 'adresse']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos/eleves', 'public');
        }

        $eleve = Eleve::create($data);

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $request->classe_id,
            'annee_scolaire' => $request->annee_scolaire,
            'date_inscription' => now(),
            'statut' => 'actif',
        ]);

        return redirect('/admin/eleves')->with('success', 'Élève ajouté avec succès !');
    }

    public function show($id)
    {
        $eleve = Eleve::with('inscriptionActuelle.classe')->findOrFail($id);
        return view('admin.eleves.show', compact('eleve'));
    }

    public function edit($id)
    {
        $eleve = Eleve::findOrFail($id);
        $classes = Classe::orderBy('niveau')->orderBy('nom')->get();
        $currentInscription = $eleve->inscriptions()->where('statut', 'actif')->latest()->first();

        return view('admin.eleves.edit', compact('eleve', 'classes', 'currentInscription'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'classe_id' => 'required|exists:classes,id',
            'nom_parent' => 'required|string|max:200',
            'contact_parent' => 'required|string|max:20',
            'adresse' => 'nullable|string',
            'annee_scolaire' => 'required|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $eleve = Eleve::findOrFail($id);
        $data = $request->only(['nom', 'prenom', 'date_naissance', 'sexe', 'nom_parent', 'contact_parent', 'adresse']);

        if ($request->hasFile('photo')) {
            if ($eleve->photo) {
                Storage::disk('public')->delete($eleve->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos/eleves', 'public');
        }

        $eleve->update($data);

        $inscription = $eleve->inscriptions()->where('statut', 'actif')->latest()->first();

        if ($inscription) {
            $inscription->update([
                'classe_id' => $request->classe_id,
                'annee_scolaire' => $request->annee_scolaire,
            ]);
        } else {
            $eleve->inscriptions()->create([
                'classe_id' => $request->classe_id,
                'annee_scolaire' => $request->annee_scolaire,
                'date_inscription' => now(),
                'statut' => 'actif',
            ]);
        }

        return redirect()->route('admin.eleves.index')->with('success', 'Élève modifié avec succès !');
    }

    public function destroy($id)
    {
        $eleve = Eleve::findOrFail($id);

        if ($eleve->photo) {
            Storage::disk('public')->delete($eleve->photo);
        }

        $eleve->delete();

        return redirect()->route('admin.eleves.index')->with('success', 'Élève supprimé avec succès !');
    }
}