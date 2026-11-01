@extends('layouts.app')

@section('title', 'Journal des épreuves')
@section('page_title', 'Journal des épreuves')

@section('content')

<p style="color:#666; font-size:14px; margin-bottom:16px; line-height:1.7;">
    Qui a ouvert, imprimé ou téléchargé une épreuve, et quand. Seuls la direction et le secrétariat sont notés (les 300 derniers accès).
</p>

<form method="GET" action="/admin/journal-epreuves" style="margin-bottom:20px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <label style="font-size:14px; color:#666;">Session :</label>
    <select name="examen_id" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
        <option value="">Toutes les sessions</option>
        @foreach($examens as $x)
        <option value="{{ $x->id }}" {{ $examenId === $x->id ? 'selected' : '' }}>{{ $x->titre }} — {{ $x->periode }}</option>
        @endforeach
    </select>
</form>

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f8f9fa;">
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Date et heure</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Qui</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Action</th>
            <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Épreuve</th>
        </tr>
    </thead>
    <tbody>
        @forelse($acces as $a)
        <tr style="border-top:1px solid #f0f0f0;">
            <td style="padding:12px 20px; font-size:13px; color:#555;">
                {{ $a->created_at->timezone('Africa/Porto-Novo')->format('d/m/Y à H:i') }}
            </td>
            <td style="padding:12px 20px; font-size:14px; color:#333;">
                @if($a->utilisateur)
                    <strong>{{ $a->utilisateur->nom }} {{ $a->utilisateur->prenom }}</strong>
                    <span style="font-size:12px; color:#888;">({{ $a->utilisateur->role }})</span>
                @else
                    <span style="color:#999;">utilisateur supprimé</span>
                @endif
            </td>
            <td style="padding:12px 20px; font-size:13px; color:#555;">
                {{ $a->action === 'impression' ? '🖨️ A ouvert pour impression' : '⬇️ A téléchargé' }}
                <span style="font-size:12px; color:#888;">({{ $a->version === 'finale' ? 'version prête' : 'original' }})</span>
            </td>
            <td style="padding:12px 20px; font-size:13px; color:#555;">
                @if($a->epreuve)
                    <strong>{{ $a->epreuve->matiere ? $a->epreuve->matiere->nom : 'Matière supprimée' }}</strong>
                    — {{ $a->epreuve->niveau }}<br>
                    <span style="font-size:12px; color:#888;">{{ $a->epreuve->examen ? $a->epreuve->examen->titre : '' }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="padding:40px; text-align:center; color:#999; font-size:14px;">Aucune consultation notée pour le moment.</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

@endsection