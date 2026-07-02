@extends('layouts.app')

@section('title', 'Élèves')

@section('menu')
    <div class="menu-label">Principal</div>
    <a href="/admin/dashboard" class="menu-item"><span>📊</span> Tableau de bord</a>

    <div class="menu-label">Gestion</div>
    <a href="/admin/utilisateurs" class="menu-item"><span>👥</span> Utilisateurs</a>
    <a href="/admin/eleves" class="menu-item active"><span>🎒</span> Élèves</a>
    <a href="/admin/classes" class="menu-item"><span>🏫</span> Classes</a>

    <div class="menu-label">Scolarité</div>
    <a href="/admin/paiements" class="menu-item"><span>💰</span> Paiements</a>
    <a href="/admin/notes" class="menu-item"><span>📝</span> Notes</a>
    <a href="/admin/absences" class="menu-item"><span>📅</span> Absences</a>

    <div class="menu-label">Secrétariat</div>
    <a href="/admin/plaintes" class="menu-item"><span>📋</span> Plaintes</a>
    <a href="/admin/recus" class="menu-item"><span>🧾</span> Reçus</a>
@endsection

@section('page_title', 'Gestion des Élèves')

@section('content')

    @if(session('success'))
        <div style="background:#e6f4ea; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h2 style="font-size:16px; color:#333;">Liste des élèves</h2>
        <a href="/admin/eleves/create" style="background:#1a73e8; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">
            + Ajouter un élève
        </a>
    </div>

    <div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8f9fa;">
                    <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Photo</th>
                    <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Nom & Prénom</th>
                    <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Sexe</th>
                    <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Date de naissance</th>
                    <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Parent/Tuteur</th>
                    <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Contact</th>
                    <th style="padding:14px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eleves as $eleve)
                <tr style="border-top:1px solid #f0f0f0; transition:background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                    <td style="padding:14px 20px;">
                        @if($eleve->photo)
                            <img src="{{ asset('storage/'.$eleve->photo) }}" style="width:42px; height:42px; border-radius:50%; object-fit:cover; border:2px solid #e8f0fe;">
                        @else
                            <div style="width:42px; height:42px; border-radius:50%; background:#e8f0fe; display:flex; align-items:center; justify-content:center; font-size:20px;">
                                {{ $eleve->sexe === 'M' ? '👦' : '👧' }}
                            </div>
                        @endif
                    </td>
                    <td style="padding:14px 20px;">
                        <div style="font-size:14px; font-weight:600; color:#333;">{{ $eleve->prenom }} {{ $eleve->nom }}</div>
                    </td>
                    <td style="padding:14px 20px;">
                        <span style="background:{{ $eleve->sexe === 'M' ? '#e8f0fe' : '#fce8f3' }}; color:{{ $eleve->sexe === 'M' ? '#1a73e8' : '#d81b60' }}; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">
                            {{ $eleve->sexe === 'M' ? 'Garçon' : 'Fille' }}
                        </span>
                    </td>
                    <td style="padding:14px 20px; font-size:14px; color:#555;">
                        {{ \Carbon\Carbon::parse($eleve->date_naissance)->format('d/m/Y') }}
                    </td>
                    <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $eleve->nom_parent }}</td>
                    <td style="padding:14px 20px; font-size:14px; color:#555;">{{ $eleve->contact_parent }}</td>
                    <td style="padding:14px 20px;">
                        <div style="display:flex; gap:6px;">
                            <a href="/admin/eleves/{{ $eleve->id }}"
                                style="background:#34a853; color:white; padding:6px 12px; border-radius:6px; font-size:12px; text-decoration:none; font-weight:600;">
                                Voir
                            </a>
                            <a href="/admin/eleves/{{ $eleve->id }}/edit"
                                style="background:#1a73e8; color:white; padding:6px 12px; border-radius:6px; font-size:12px; text-decoration:none; font-weight:600;">
                                Modifier
                            </a>
                            <form method="POST" action="/admin/eleves/{{ $eleve->id }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Supprimer cet élève ?')"
                                    style="background:#ea4335; color:white; border:none; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-weight:600;">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:40px; text-align:center; color:#999; font-size:14px;">
                        🎒 Aucun élève trouvé. <a href="/admin/eleves/create" style="color:#1a73e8;">Ajouter le premier élève</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection