<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recu;
use App\Models\Paiement;

class RecuController extends Controller
{
    public function index()
    {
        $recus = Recu::with(['paiement.eleve', 'secretaire'])
            ->orderBy('date_emission', 'desc')
            ->get();
        return view('admin.recus.index', compact('recus'));
    }

    public function create()
    {
        $paiements = Paiement::with('eleve')->orderBy('date_paiement', 'desc')->get();
        return view('admin.recus.create', compact('paiements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paiement_id'   => 'required|exists:paiements,id',
            'date_emission' => 'required|date',
        ]);

        Recu::create([
            'paiement_id'   => $request->paiement_id,
            'secretaire_id' => session('utilisateur_id'),
            'numero_recu'   => 'RECU-' . date('Y') . '-' . str_pad(Recu::count() + 1, 5, '0', STR_PAD_LEFT),
            'date_emission' => $request->date_emission,
        ]);

        return redirect('/admin/recus')->with('success', 'Reçu généré avec succès !');
    }

    public function show($id)
    {
        $recu = Recu::with(['paiement.eleve', 'paiement.typeFrais', 'secretaire'])->findOrFail($id);
        return view('admin.recus.show', compact('recu'));
    }

    public function destroy($id)
    {
        Recu::findOrFail($id)->delete();
        return redirect('/admin/recus')->with('success', 'Reçu supprimé !');
    }
}