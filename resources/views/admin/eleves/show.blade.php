@extends('layouts.app')

@section('title', 'Fiche Élève')
@section('page_title', 'Fiche de l\'Élève')

@section('content')
<div style="max-width: 700px;">

    <div style="display: flex; gap: 12px; margin-bottom: 20px;">
        <a href="{{ route('admin.eleves.edit', $eleve->id) }}"
           style="background: #1a73e8; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
            ✏️ Modifier
        </a>
        <a href="{{ route('admin.eleves.index') }}"
           style="background: #f0f0f0; color: #333; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px;">
            ← Retour à la liste
        </a>
    </div>

    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

        <div style="display: flex; gap: 24px; align-items: flex-start; margin-bottom: 24px;">
            @if($eleve->photo)
                <img src="{{ asset('storage/' . $eleve->photo) }}"
                     alt="Photo de l'élève"
                     style="width: 110px; height: 110px; object-fit: cover; border-radius: 12px; border: 1px solid #eee;">
            @else
                <div style="width: 110px; height: 110px; border-radius: 12px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 32px; color: #bbb;">
                    👤
                </div>
            @endif

            <div>
                <div style="font-size: 20px; font-weight: 700; color: #333;">{{ $eleve->prenom }} {{ $eleve->nom }}</div>
                <div style="margin-top: 6px;">
                    <span style="background: #e8f0fe; color: #1a73e8; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
    {{ $eleve->inscriptionActuelle->classe->nom ?? 'Aucune classe' }}
    @if($eleve->inscriptionActuelle && $eleve->inscriptionActuelle->classe && $eleve->inscriptionActuelle->classe->niveau)
        ({{ $eleve->inscriptionActuelle->classe->niveau }})
    @endif
</span>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px 30px;">
            <div>
            <div style="font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Matricule</div>
            <div style="font-size: 14px; color: #333;">{{ $eleve->matricule ?? 'N/A' }}</div>
           </div>
           
            <div>
                <div style="font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Date de naissance</div>
                <div style="font-size: 14px; color: #333;">{{ \Carbon\Carbon::parse($eleve->date_naissance)->format('d/m/Y') }}</div>
            </div>
            
            <div>
                <div style="font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Sexe</div>
                <div style="font-size: 14px; color: #333;">{{ $eleve->sexe == 'M' ? 'Masculin' : 'Féminin' }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Nom du parent</div>
                <div style="font-size: 14px; color: #333;">{{ $eleve->nom_parent }}</div>
            </div>
            <div>
                <div style="font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Contact parent</div>
                <div style="font-size: 14px; color: #333;">{{ $eleve->contact_parent }}</div>
            </div>
            <div style="grid-column: 1 / -1;">
                <div style="font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Adresse</div>
                <div style="font-size: 14px; color: #333;">{{ $eleve->adresse ?? 'N/A' }}</div>
            </div>
        </div>

    </div>
</div>
@endsection