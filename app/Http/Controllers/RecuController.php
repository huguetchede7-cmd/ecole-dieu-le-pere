<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recu;
use App\Models\Paiement;
use App\Models\Inscription;

class RecuController extends Controller
{
    public function index()
    {
        $recus = Recu::with(['inscription.eleve', 'paiements', 'secretaire'])
            ->orderBy('date_emission', 'desc')
            ->get();
        return view('admin.recus.index', compact('recus'));
    }

    public function create()
    {
        $inscriptions = Inscription::with('eleve')->orderBy('date_inscription', 'desc')->get();
        // Paiements pas encore rattachés à un reçu (ex: 2e tranche payée plus tard)
        $paiementsLibres = Paiement::with(['eleve', 'typeFrais'])
            ->whereNull('recu_id')
            ->orderBy('date_paiement', 'desc')
            ->get();
        return view('admin.recus.create', compact('inscriptions', 'paiementsLibres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'date_emission'  => 'required|date',
            'paiement_ids'   => 'nullable|array',
            'paiement_ids.*' => 'exists:paiements,id',
        ]);

        $recu = Recu::create([
            'inscription_id' => $request->inscription_id,
            'secretaire_id'  => session('utilisateur_id'),
            'numero_recu'    => 'RECU-' . date('Y') . '-' . str_pad(Recu::count() + 1, 5, '0', STR_PAD_LEFT),
            'date_emission'  => $request->date_emission,
        ]);

        if ($request->filled('paiement_ids')) {
            Paiement::whereIn('id', $request->paiement_ids)
                ->whereNull('recu_id')
                ->update(['recu_id' => $recu->id]);
        }

        return redirect()->route('admin.recus.show', $recu->id)->with('success', 'Reçu généré avec succès !');
    }

    public function show($id)
    {
        $recu = Recu::with(['inscription.eleve', 'inscription.classe', 'paiements.typeFrais', 'secretaire'])
            ->findOrFail($id);
        return view('admin.recus.show', compact('recu'));
    }

    public function destroy($id)
    {
        Recu::findOrFail($id)->delete();
        return redirect('/admin/recus')->with('success', 'Reçu supprimé !');
    }
}