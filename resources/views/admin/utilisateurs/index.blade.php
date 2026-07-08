@extends('layouts.app')

@section('title', 'Utilisateurs')

@section('page_title', 'Gestion des Utilisateurs')

@section('content')

    @if(session('success'))
        <div style="background: #e6f4ea; color: #2e7d32; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-size: 16px; color: #333;">Liste des utilisateurs</h2>
        <a href="/admin/utilisateurs/create" style="background: #1a73e8; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px;">
            + Ajouter un utilisateur
        </a>
    </div>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">#</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Nom complet</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Email</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Rôle</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Téléphone</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Statut</th>
                    <th style="padding: 14px 20px; text-align: left; font-size: 13px; color: #666;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($utilisateurs as $u)
                <tr style="border-top: 1px solid #f0f0f0;">
                    <td style="padding: 14px 20px; font-size: 14px; color: #333;">{{ $u->id }}</td>
                    <td style="padding: 14px 20px; font-size: 14px; color: #333;">{{ $u->prenom }} {{ $u->nom }}</td>
                    <td style="padding: 14px 20px; font-size: 14px; color: #333;">{{ $u->email }}</td>
                    <td style="padding: 14px 20px;">
                        <span style="background: #e8f0fe; color: #1a73e8; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                            {{ ucfirst($u->role) }}
                        </span>
                    </td>
                    <td style="padding: 14px 20px; font-size: 14px; color: #333;">{{ $u->telephone ?? '-' }}</td>
                    <td style="padding: 14px 20px;">
                        <span style="background: {{ $u->statut === 'actif' ? '#e6f4ea' : '#fce8e6' }}; color: {{ $u->statut === 'actif' ? '#2e7d32' : '#c62828' }}; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                            {{ ucfirst($u->statut) }}
                        </span>
                    </td>
                    <td style="padding: 14px 20px;">
                        <form method="POST" action="/admin/utilisateurs/{{ $u->id }}/statut" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: #fbbc04; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">
                                {{ $u->statut === 'actif' ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                        @if($u->id !== session('utilisateur_id'))
                        <form method="POST" action="/admin/utilisateurs/{{ $u->id }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Supprimer cet utilisateur ?')" style="background: #ea4335; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; margin-left: 4px;">
                                Supprimer
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 30px; text-align: center; color: #999; font-size: 14px;">
                        Aucun utilisateur trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection