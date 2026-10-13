@extends('layouts.app')

@section('title', 'Livret de caisse')
@section('page_title', 'Livret de caisse')

@push('styles')
<style>
#entete-impression { display: none; }

@media print {
    body * { visibility: hidden; }
    #zone-impression, #zone-impression * { visibility: visible; }
    #zone-impression {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    #entete-impression { display: block !important; }
    .no-print { display: none !important; }
    #zone-impression table { font-size: 11px; }
    @page { size: A4 landscape; margin: 10mm; }
}
</style>
@endpush

@section('content')

<div class="no-print" style="background: white; border-radius: 12px; padding: 20px 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px;">
    <form method="GET" action="{{ url('/admin/livret-caisse') }}" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">

        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Période</label>
            <select name="mode" id="mode" onchange="afficherChamp()"
                style="padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                <option value="jour" {{ $mode === 'jour' ? 'selected' : '' }}>Jour</option>
                <option value="mois" {{ $mode === 'mois' ? 'selected' : '' }}>Mois</option>
                <option value="annee" {{ $mode === 'annee' ? 'selected' : '' }}>Année scolaire</option>
            </select>
        </div>

        <div id="champ-jour">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Date</label>
            <input type="date" name="date" value="{{ $date }}"
                style="padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        </div>

        <div id="champ-mois">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Mois</label>
            <input type="month" name="mois" value="{{ $mois }}"
                style="padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        </div>

        <div id="champ-annee">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px;">Année scolaire</label>
            <select name="annee_scolaire"
                style="padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
                @foreach($anneesDisponibles as $a)
                <option value="{{ $a }}" {{ $anneeScolaire === $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
            style="padding: 10px 20px; border: none; background: #e8a317; color: white; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
            Afficher
        </button>

        <button type="button" onclick="window.print()"
            style="padding: 10px 20px; border: none; background: #1a73e8; color: white; border-radius: 8px; font-size: 14px; cursor: pointer; margin-left: auto;">
            🖨️ Imprimer / PDF
        </button>
    </form>
</div>

<div id="zone-impression">

    <div id="entete-impression" style="text-align: center; margin-bottom: 14px;">
        <div style="font-size: 18px; font-weight: 700;">ÉCOLE DIEU LE PÈRE — LIVRET DE CAISSE</div>
        <div style="font-size: 14px;">{{ $libelle }}</div>
    </div>

    <div style="font-size: 15px; font-weight: 600; color: #333; margin-bottom: 12px;">{{ $libelle }}</div>

    <div style="display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 20px;">
        <div style="flex: 1; min-width: 200px; background: white; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 12px; color: #888;">💰 Total encaissé</div>
            <div style="font-size: 22px; font-weight: 700; color: #2e7d32;">{{ number_format($total, 0, ',', ' ') }} FCFA</div>
        </div>
        <div style="flex: 1; min-width: 200px; background: white; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 12px; color: #888;">📋 Reçus émis</div>
            <div style="font-size: 22px; font-weight: 700; color: #333;">{{ $nbRecus }}</div>
        </div>
        <div style="flex: 1; min-width: 200px; background: white; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 12px; color: #888;">👨‍🎓 Élèves ayant payé</div>
            <div style="font-size: 22px; font-weight: 700; color: #333;">{{ $nbEleves }}</div>
        </div>
    </div>

        <div style="display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 20px;">
        <div style="flex: 1; min-width: 200px; background: white; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 12px; color: #888;">💵 Espèces</div>
            <div style="font-size: 20px; font-weight: 700; color: #333;">{{ number_format($totalEspeces, 0, ',', ' ') }} FCFA</div>
        </div>
        <div style="flex: 1; min-width: 200px; background: white; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 12px; color: #888;">📱 Mobile Money</div>
            <div style="font-size: 20px; font-weight: 700; color: #333;">{{ number_format($totalMobileMoney, 0, ',', ' ') }} FCFA</div>
        </div>
        @if($totalAutres > 0)
        <div style="flex: 1; min-width: 200px; background: white; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 12px; color: #888;">Autres modes</div>
            <div style="font-size: 20px; font-weight: 700; color: #333;">{{ number_format($totalAutres, 0, ',', ' ') }} FCFA</div>
        </div>
        @endif
    </div>

    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #eee; color: #666; font-size: 12px; text-transform: uppercase;">
                    <th style="padding: 10px 8px;">Date</th>
                    <th style="padding: 10px 8px;">Élève</th>
                    <th style="padding: 10px 8px;">Classe</th>
                    <th style="padding: 10px 8px;">Type de frais</th>
                    <th style="padding: 10px 8px; text-align: right;">Montant</th>
                    <th style="padding: 10px 8px;">N° Reçu</th>
                    <th style="padding: 10px 8px;">Comptable</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paiements as $p)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 10px 8px; white-space: nowrap;">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}</td>
                    <td style="padding: 10px 8px;">{{ $p->eleve ? strtoupper($p->eleve->nom) . ' ' . $p->eleve->prenom : '—' }}</td>
                    <td style="padding: 10px 8px;">{{ $p->inscription?->classe?->nom ?? '—' }}</td>
                    <td style="padding: 10px 8px;">{{ $p->typeFrais->libelle ?? '—' }}</td>
                    <td style="padding: 10px 8px; text-align: right; white-space: nowrap;">{{ number_format($p->montant_paye, 0, ',', ' ') }} FCFA</td>
                    <td style="padding: 10px 8px; white-space: nowrap;">
                        @if($p->recu)
                        <a href="{{ url('/admin/recus/' . $p->recu->id) }}" style="color: #1a56a0;">{{ $p->recu->numero_recu }}</a>
                        @else
                        —
                        @endif
                    </td>
                    <td style="padding: 10px 8px;">{{ $p->comptable ? $p->comptable->nom . ' ' . $p->comptable->prenom : '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 24px; text-align: center; color: #999;">Aucun encaissement sur cette période.</td>
                </tr>
                @endforelse
            </tbody>
            @if($paiements->count())
            <tfoot>
                <tr style="border-top: 2px solid #333; font-weight: 700;">
                    <td colspan="4" style="padding: 12px 8px; text-align: right;">TOTAL GÉNÉRAL DE LA PÉRIODE</td>
                    <td style="padding: 12px 8px; text-align: right; white-space: nowrap;">{{ number_format($total, 0, ',', ' ') }} FCFA</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<script>
function afficherChamp() {
    const m = document.getElementById('mode').value;
    document.getElementById('champ-jour').style.display = m === 'jour' ? '' : 'none';
    document.getElementById('champ-mois').style.display = m === 'mois' ? '' : 'none';
    document.getElementById('champ-annee').style.display = m === 'annee' ? '' : 'none';
}
afficherChamp();
</script>

@endsection
