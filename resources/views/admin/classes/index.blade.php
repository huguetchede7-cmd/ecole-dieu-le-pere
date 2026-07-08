@extends('layouts.app')

@section('title', 'Classes')

@section('page_title', 'Gestion des Classes')

@section('content')

    @if(session('success'))
        <div style="background: #e6f4ea; color: #2e7d32; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-size: 16px; color: #333;">Liste des classes</h2>
        <a href="/admin/classes/create" style="background: #1a73e8; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px;">
            + Ajouter une classe
        </a>
    </div>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">#</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Nom</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Niveau</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Année scolaire</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Capacité</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Effectif</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $classe)
                <tr style="border-top: 1px solid #f0f0f0;">
                    <td style="padding: 14px 20px; font-size: 14px;">{{ $classe->id }}</td>
                    <td style="padding: 14px 20px; font-size: 14px; font-weight: 600;">{{ $classe->nom }}</td>
                    <td style="padding: 14px 20px;">
                        <span style="background: #e8f0fe; color: #1a73e8; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                            {{ $classe->niveau }}
                        </span>
                    </td>
                    <td style="padding: 14px 20px; font-size: 14px;">{{ $classe->annee_scolaire }}</td>
                    <td style="padding: 14px 20px; font-size: 14px;">{{ $classe->capacite }} élèves</td>
                    <td style="padding: 14px 20px; font-size: 14px;">
                        <span style="font-weight: 700; color: #1a73e8;">{{ $classe->effectif() }}</span> / {{ $classe->capacite }}
                    </td>
                    <td style="padding: 14px 20px;">
                        <a href="/admin/classes/{{ $classe->id }}/edit"
                            style="background: #1a73e8; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; text-decoration: none; margin-right: 4px;">
                            Modifier
                        </a>
                        <form method="POST" action="/admin/classes/{{ $classe->id }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Supprimer cette classe ?')"
                                style="background: #ea4335; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 30px; text-align: center; color: #999; font-size: 14px;">
                        Aucune classe trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection