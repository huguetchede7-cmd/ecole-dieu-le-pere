<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametre;

class ParametreController extends Controller
{
    public function index()
    {
        $ecole = Parametre::ecole();
        $anneeCourante = Parametre::anneeCourante();

        $anneesDisponibles = [];
        for ($d = 2023; $d <= (int) date('Y') + 1; $d++) {
            $anneesDisponibles[] = $d . '-' . ($d + 1);
        }

        return view('admin.parametres.index', compact('ecole', 'anneeCourante', 'anneesDisponibles'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ecole_nom' => 'required|string|max:255',
            'ecole_ministere' => 'nullable|string|max:255',
            'ecole_autorisation' => 'nullable|string|max:255',
            'ecole_telephone' => 'nullable|string|max:100',
            'ecole_bp' => 'nullable|string|max:100',
            'ecole_email' => 'nullable|email|max:255',
            'annee_scolaire_courante' => ['required', 'regex:/^\d{4}-\d{4}$/'],
        ]);

        [$a, $b] = explode('-', $request->annee_scolaire_courante);
        if ((int) $b !== (int) $a + 1) {
            return back()->withErrors(['annee_scolaire_courante' => "L'année scolaire doit être de la forme 2026-2027."])->withInput();
        }

        foreach ([
            'ecole_nom', 'ecole_ministere', 'ecole_autorisation',
            'ecole_telephone', 'ecole_bp', 'ecole_email', 'annee_scolaire_courante',
        ] as $cle) {
            Parametre::updateOrCreate(['cle' => $cle], ['valeur' => $request->input($cle)]);
        }

        return redirect()->route('admin.parametres.index')->with('success', 'Paramètres enregistrés avec succès !');
    }
}
