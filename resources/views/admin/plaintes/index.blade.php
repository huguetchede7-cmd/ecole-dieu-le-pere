@extends('layouts.app')

@section('title', 'Plaintes')

@section('page_title', 'Gestion des Plaintes')

@section('content')

@if(session('success'))
<div style="background: #e6f4ea; color: #2e7d32; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
    ✅ {{ session('success') }}
</div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h2 style="font-size: 16px; color: #333;">Liste des plaintes</h2>
    <a href="/admin/plaintes/create" style="background: #1a73e8; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px;">
        + Déposer une plainte
    </a>
</div>

<div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa;">
                <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">#</th>
                <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Élève</th>
                <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Description</th>
                <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Date</th>
                <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Statut</th>
                <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plaintes as $plainte)
            <tr style="border-top: 1px solid #f0f0f0;">
                <td style="padding: 14px 20px; font-size: 14px;">{{ $plainte->id }}</td>
                <td style="padding: 14px 20px; font-size: 14px; font-weight: 600;">
                    {{ $plainte->eleve->nom ?? 'N/A' }} {{ $plainte->eleve->prenom ?? '' }}
                </td>
                <td style="padding: 14px 20px; font-size: 14px; color: #666; max-width: 250px;">
                    {{ Str::limit($plainte->description, 60) }}
                </td>
                <td style="padding: 14px 20px; font-size: 14px;">{{ $plainte->date_plainte }}</td>
                <td style="padding: 14px 20px;">
                    @if($plainte->statut === 'en_cours')
                    <span style="background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">En cours</span>
                    @elseif($plainte->statut === 'resolue')
                    <span style="background: #e6f4ea; color: #2e7d32; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Résolue</span>
                    @else
                    <span style="background: #fdecea; color: #c62828; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Rejetée</span>
                    @endif
                </td>
                <td style="padding: 14px 20px;">
                    <a href="/admin/plaintes/{{ $plainte->id }}/edit"
                        style="background: #1a73e8; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; text-decoration: none; margin-right: 4px;">
                        Traiter
                    </a>
                    <form method="POST" action="/admin/plaintes/{{ $plainte->id }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Supprimer cette plainte ?')"
                            style="background: #ea4335; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 30px; text-align: center; color: #999; font-size: 14px;">
                    Aucune plainte enregistrée.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
