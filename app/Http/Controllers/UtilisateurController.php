<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;

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
        return view('admin.utilisateurs.create');
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
        ]);

        Utilisateur::create([
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'email'        => $request->email,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'role'         => $request->role,
            'statut'       => 'actif',
            'telephone'    => $request->telephone,
        ]);

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