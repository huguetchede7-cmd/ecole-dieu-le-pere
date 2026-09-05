@extends('layouts.app')

@section('title', 'Bulletin')
@section('page_title', 'Bulletin de ' . $eleve->prenom . ' ' . $eleve->nom)

@section('content')
<div style="max-width: 850px;">

<div style="display: flex; gap: 12px; margin-bottom: 20px;">
<a href="{{ route('admin.eleves.show', $eleve->id) }}"
style="background: #f0f0f0; color: #333; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px;">
← Retour à la fiche élève
</a>
</div>

<div style="border: 6px double #1a73e8; padding: 8px; border-radius: 14px;">
<div style="position: relative; overflow: hidden; border: 2px solid #1a73e8; border-radius: 6px; padding: 24px; background: linear-gradient(white, white), repeating-linear-gradient(45deg, #f5f8ff 0px, #f5f8ff 2px, white 2px, white 14px);">

<img src="{{ asset('images/logo-ecole-dieu-le-pere.svg') }}" alt="" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 420px; opacity: 0.08; z-index: 0; pointer-events: none;">

<div style="margin-bottom: 24px; border-bottom: 2px solid #1a73e8; padding-bottom: 16px; background: linear-gradient(135deg, #eef4fd, #fdf6e3); border-radius: 10px; padding-top: 16px; padding-left: 16px; padding-right: 16px;">
<div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;">

<img src="{{ asset('images/armoiries-benin.png') }}" alt="Armoiries du Bénin" style="width: 95px; height: auto; margin-top: 6px;">

<div style="text-align: center; flex: 1;">

<h2> <p style="font-size: 15px; color: #333333; margin-bottom: 2px;">RÉPUBLIQUE DU BÉNIN</p></h2>

<h2> <p style="font-size: 15px; color: #666; margin-bottom: 10px;">MINISTÈRE DES ENSEIGNEMENTS MATERNELS ET PRIMAIRES</p></h2>

<h2> <p style="font-size: 15px; color: #333; margin-bottom: 4px;">ECOLE PRIMAIRE PRIVÉE DIEU LE PÈRE</p></h2>

<h2> <p style="font-size: 15px; color: #666; margin-bottom: 2px;">AUTORISATION N° 049/MEMP/CAB/DC/SGM/DPP/DGS/DEPES/SA/2005</p></h2>

<h2> <p style="font-size: 15px; color: #66fc; margin-bottom: 12px;">Tél:23767777 BP:08 Parakou Email: dieulepere@yahoo.fr</p></h2>

<h3 style="font-size: 20px; font-weight: 800; color: #333; margin-top: 14px; margin-bottom: 16px; text-transform: uppercase;">
BULLETIN DE NOTES
</h3>

<div style="display: inline-block; text-align: left; margin: 0 auto;">
<table style="border-collapse: collapse;">
<tr>
<td style="padding: 3px 0; font-size: 13px; color: #333; font-weight: 700; white-space: nowrap;">NOM ET PRÉNOM :</td>
<td style="padding: 3px 0 3px 10px; font-size: 13px; color: #333;">{{ strtoupper($eleve->nom . ' ' . $eleve->prenom) }}</td>
</tr>
<tr>
<td style="padding: 3px 0; font-size: 13px; color: #333; font-weight: 700; white-space: nowrap;">DATE ET LIEU DE NAISSANCE :</td>
<td style="padding: 3px 0 3px 10px; font-size: 13px; color: #333;">
{{ $eleve->date_naissance ? \Carbon\Carbon::parse($eleve->date_naissance)->format('d/m/Y') : 'N/A' }}
à {{ $eleve->lieu_naissance ?? 'N/A' }}
</td>
</tr>
<tr>
<td style="padding: 3px 0; font-size: 13px; color: #333; font-weight: 700; white-space: nowrap;">ANNEE SCOLAIRE:</td>
<td style="padding: 3px 0 3px 10px; font-size: 13px; color: #333;">{{ $anneeScolaire }}</td>
</tr>
<tr>
<td style="padding: 3px 0; font-size: 13px; color: #333; font-weight: 700; white-space: nowrap;">CLASSE:</td>
<td style="padding: 3px 0 3px 10px; font-size: 13px; color: #333;">{{ $classeNom }}</td>
</tr>
<tr>
<td style="padding: 3px 0; font-size: 12px; color: #999; font-weight: 700; white-space: nowrap;">Matricule :</td>
<td style="padding: 3px 0 3px 10px; font-size: 12px; color: #999;">{{ $eleve->matricule ?? 'N/A' }}</td>
</tr>
</table>
</div>

</div>

<img src="{{ asset('images/logo-ecole-dieu-le-pere.svg') }}" alt="Logo de l'école" style="width: 95px; height: auto; margin-top: 6px;">

</div>
</div>

<table style="width: 100%; border-collapse: collapse; font-size: 13px;">
<thead>
<tr style="background: #f8f9fa;">
<th style="padding: 10px 12px; text-align: left; color: #666;">Matière</th>
@foreach($periodes as $periode)
<th style="padding: 10px 12px; text-align: center; color: #666; text-transform: capitalize;">{{ $periode }}</th>
@endforeach
</tr>
</thead>
<tbody>
@forelse($tableau as $ligne)
<tr style="border-top: 1px solid #f0f0f0;">
<td style="padding: 9px 12px; color: #333;">{{ $ligne['nom'] }}</td>
@foreach($periodes as $periode)
<td style="padding: 9px 12px; text-align: center; color: #333; font-weight: 600;">
{{ $ligne[$periode] ?? '—' }}
</td>
@endforeach
</tr>
@empty
<tr>
<td colspan="{{ count($periodes) + 1 }}" style="padding: 20px; text-align: center; color: #999;">
Aucune matière trouvée.
</td>
</tr>
@endforelse
</tbody>
<tfoot>
<tr style="border-top: 2px solid #1a73e8; background: #f8f9fa;">
<td style="padding: 10px 12px; font-weight: 700; color: #1a73e8;">Moyenne trimestrielle</td>
@foreach($periodes as $periode)
<td style="padding: 10px 12px; text-align: center; font-weight: 700; color: #1a73e8;">
{{ $moyennesTrimestrielles[$periode] ?? '—' }}
</td>
@endforeach
</tr>
<tr style="background: #f8f9fa;">
<td style="padding: 8px 12px; font-weight: 600; color: #666; font-size: 12px;">Appréciation</td>
@foreach($periodes as $periode)
<td style="padding: 8px 12px; text-align: center; color: #666; font-size: 12px;">
{{ $appreciationsTrimestrielles[$periode] ?? '—' }}
</td>
@endforeach
</tr>
</tfoot>
</table>

<div style="border: 2px solid #1a73e8; border-radius: 10px; padding: 20px; margin-top: 24px;">
<h4 style="font-size: 14px; color: #1a73e8; margin-bottom: 14px; text-align: center;">Bilan annuel</h4>

<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; text-align: center;">
<div>
<div style="font-size: 11px; color: #999; text-transform: uppercase; margin-bottom: 4px;">Moyenne annuelle</div>
<div style="font-size: 18px; font-weight: 700; color: #333;">{{ $moyenneAnnuelle ?? 'En attente' }}{{ $moyenneAnnuelle !== null ? ' / 20' : '' }}</div>
</div>
<div>
<div style="font-size: 11px; color: #999; text-transform: uppercase; margin-bottom: 4px;">Appréciation</div>
<div style="font-size: 15px; font-weight: 600; color: #333;">{{ $appreciationAnnuelle ?? 'En attente' }}</div>
</div>
<div>
<div style="font-size: 11px; color: #999; text-transform: uppercase; margin-bottom: 4px;">Décision</div>
@if($decision === 'admis')
<span style="padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; background:#e6f4ea; color:#2e7d32;">✅ ADMIS(E)</span>
@elseif($decision === 'refuse')
<span style="padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; background:#fdecea; color:#c62828;">❌ REFUSÉ(E)</span>
@else
<span style="padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; background:#f0f0f0; color:#999;">⏳ En attente</span>
@endif
</div>
</div>
</div>

<div style="display: flex; gap: 20px; margin-top: 24px;">

<div style="flex: 1; border: 1px solid #ddd; border-radius: 10px; padding: 18px;">
<h4 style="font-size: 13px; color: #333; margin-bottom: 12px;">Récompenses / Sanctions</h4>

<label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #333; margin-bottom: 8px;">
<input type="checkbox"> Félicitations
</label>
<label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #333; margin-bottom: 8px;">
<input type="checkbox"> Encouragement
</label>
<label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #333; margin-bottom: 8px;">
<input type="checkbox"> Tableau d'honneur
</label>
<label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #333; margin-bottom: 8px;">
<input type="checkbox"> Avertissement Travail/Discipline
</label>
<label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #333;">
<input type="checkbox"> Blâme Travail/Discipline
</label>
</div>

<div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
<div style="border: 1px solid #ddd; border-radius: 10px; padding: 18px; margin-bottom: 12px;">
<div style="font-size: 12px; color: #999; margin-bottom: 40px;">Nom et signature du Maître</div>
<div style="border-top: 1px solid #ccc;"></div>
</div>

<div style="border: 1px solid #ddd; border-radius: 10px; padding: 18px;">
<div style="font-size: 12px; color: #999; margin-bottom: 90px;">Nom et signature du Directeur</div>
<div style="border-top: 1px solid #ccc;"></div>
</div>
</div>

</div>

<div style="margin-top: 24px; text-align: center;">
<button onclick="window.print()" style="background: #1a73e8; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 14px; cursor: pointer;">
🖨️ Imprimer
</button>
</div>

</div>
</div>
</div>
@endsection