<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use App\Models\Classe;
use App\Models\Parametre;

class UtilisateurController extends Controller
{
    // Liste des utilisateurs
    public function index()
    {
        $utilisateurs = Utilisateur::orderBy('created_at', 'desc')->get();
        return view('admin.utilisateurs.index', compact('utilisateurs'));
    }

        // Formulaire d'ajout
    public function create()
    {
        $ordre = ['Maternelle 1', 'Maternelle 2', 'CI', 'CP', 'CE1', 'CE2', 'CM1', 'CM2'];

        // Classes de l'année en cours qui n'ont pas encore d'enseignant
        $classesLibres = Classe::whereNull('enseignant_id')
            ->where('annee_scolaire', Parametre::anneeCourante())
            ->get()
            ->sortBy(fn($c) => [array_search($c->niveau, $ordre), $c->nom])
            ->values();

        return view('admin.utilisateurs.create', compact('classesLibres'));
    }

       // Enregistrer un utilisateur
    public function store(Request $request)
    {
        $request->validate([
            'nom'          => 'required|string|max:100',
            'prenom'       => 'required|string|max:100',
            'email'        => 'required|email|unique:utilisateurs,email',
            'mot_de_passe' => 'required|min:6',
            'role'         => 'required|in:admin,directeur,enseignant,comptable,secretaire',
            'telephone'    => 'nullable|string|max:20',
            'classe_id'    => 'required_if:role,enseignant|nullable|exists:classes,id',
            'civilite'     => 'nullable|in:Monsieur,Madame,Mademoiselle',
        ], [
            'classe_id.required_if' => 'Choisissez la classe de cet enseignant.',
        ]);

        $classe = null;
        if ($request->role === 'enseignant') {
            $classe = Classe::findOrFail($request->classe_id);

            if ($classe->enseignant_id) {
                return back()->withErrors(['classe_id' => 'Cette classe a déjà un enseignant.'])->withInput();
            }
        }

        $utilisateur = Utilisateur::create([
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'email'        => $request->email,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'role'         => $request->role,
            'statut'       => 'actif',
            'telephone'    => $request->telephone,
            'civilite'     => $request->civilite,
        ]);

        if ($classe) {
            $classe->update(['enseignant_id' => $utilisateur->id]);
        }

        return redirect('/admin/utilisateurs')->with('success', 'Utilisateur créé avec succès !');
    }

    // Changer le statut actif/inactif
    public function toggleStatut($id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->statut = $utilisateur->statut === 'actif' ? 'inactif' : 'actif';
        $utilisateur->save();

        return redirect('/admin/utilisateurs')->with('success', 'Statut mis à jour !');
    }

    // Supprimer un utilisateur
    public function destroy($id)
    {
        Utilisateur::findOrFail($id)->delete();
        return redirect('/admin/utilisateurs')->with('success', 'Utilisateur supprimé !');
    }
}