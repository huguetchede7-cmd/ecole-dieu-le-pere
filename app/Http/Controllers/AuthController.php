<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;

class AuthController extends Controller
{
    // Afficher le formulaire de connexion
    public function showLogin()
    {
        return view('auth.login');
    }

    // Traiter la connexion
    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'mot_de_passe' => 'required'
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)
                                  ->where('statut', 'actif')
                                  ->first();

        if (!$utilisateur || !Hash::check($request->mot_de_passe, $utilisateur->mot_de_passe)) {
            return back()->withErrors([
                'email' => 'Email ou mot de passe incorrect.'
            ]);
        }

        session([
            'utilisateur_id'   => $utilisateur->id,
            'utilisateur_nom'  => $utilisateur->nom,
            'utilisateur_role' => $utilisateur->role
        ]);

        // Redirection selon le rôle
        return match($utilisateur->role) {
            'admin'      => redirect('/admin/dashboard'),
            'directeur'  => redirect('/directeur/dashboard'),
            'enseignant' => redirect('/enseignant/dashboard'),
            'comptable'  => redirect('/comptable/dashboard'),
            'secretaire' => redirect('/secretaire/dashboard'),
        };
    }

    // Déconnexion
    public function logout()
    {
        session()->flush();
        return redirect('/login');
    }
}