@extends('layouts.app')

@section('title', 'Échéances')
@section('page_title', 'Gestion des Échéances')

@section('content')

@if(session('success'))
<div style="background:#e6f4ea; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
✅ {{ session('success') }}
</div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <h2 style="font-size:16px; color:#333;">Liste des échéances</h2>
    <a href="{{ route('admin.echeances.create') }}" style="background:#1a73e8; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">
        + Ajouter une échéance
    </a>
</div>

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f8f9fa;">
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Libellé</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Année scolaire</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Date limite</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Montant</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($echeances as $echeance)
        <tr style="border-top:1px solid #f0f0f0;">
            <td style="padding:14px 20px; font-size:14px; font-weight:600; color:#333;">{{ $echeance->libelle }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $echeance->annee_scolaire }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ \Carbon\Carbon::parse($echeance->date_limite)->format('d/m/Y') }}</td>
            <td style="padding:14px 20px; font-size:14px; color:#555;">{{ number_format($echeance->montant, 0, ',', ' ') }} FCFA</td>
            <td style="padding:14px 20px;">
                <div style="display:flex; gap:6px;">
                    <a href="{{ route('admin.echeances.edit', $echeance->id) }}" style="background:#1a73e8; color:white; padding:6px 12px; border-radius:6px; font-size:12px; text-decoration:none; font-weight:600;">Modifier</a>
                    <form method="POST" action="{{ route('admin.echeances.destroy', $echeance->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Supprimer cette échéance ?')" style="background:#ea4335; color:white; border:none; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-weight:600;">Supprimer</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="padding:40px; text-align:center; color:#999; font-size:14px;">Aucune échéance enregistrée.</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

@endsection