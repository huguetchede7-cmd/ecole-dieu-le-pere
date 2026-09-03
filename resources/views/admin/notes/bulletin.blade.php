@extends('layouts.app')

@section('title', 'Bulletin')
@section('page_title', 'Bulletin de ' . $eleve->prenom . ' ' . $eleve->nom)

@section('content')
<div style="max-width: 750px;">

<div style="display: flex; gap: 12px; margin-bottom: 20px;">
    <a href="{{ route('admin.eleves.show', $eleve->id) }}"
        style="background: #f0f0f0; color: #333; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px;">
        ← Retour à la fiche élève
    </a>
</div>

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

    <div style="text-align: center; margin-bottom: 24px; border-bottom: 2px solid #1a73e8; padding-bottom: 16px;">
        <h2 style="font-size: 18px; color: #1a73e8;">🏫 École Dieu le Père</h2>
        <p style="font-size: 14px; color: #333; margin-top: 6px;">Bulletin — {{ $eleve->prenom }} {{ $eleve->nom }}</p>
        <p style="font-size: 12px; color: #999;">Matricule : {{ $eleve->matricule ?? 'N/A' }} — Année scolaire : {{ $anneeScolaire }}</p>
    </div>

    @foreach($bulletin as $periode => $data)
    <div style="margin-bottom: 24px;">
        <h4 style="font-size: 14px; color: #333; margin-bottom: 10px; text-transform: capitalize;">{{ $periode }}</h4>

        @if(count($data['matieres']) > 0)
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 8px 12px; text-align: left; color: #666;">Matière</th>
                    <th style="padding: 8px 12px; text-align: right; color: #666;">Moyenne /20</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['matieres'] as $m)
                <tr style="border-top: 1px solid #f0f0f0;">
                    <td style="padding: 8px 12px; color: #333;">{{ $m['matiere'] }}</td>
                    <td style="padding: 8px 12px; text-align: right; color: #333; font-weight: 600;">{{ $m['moyenne'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="display: flex; justify-content: flex-end; margin-top: 10px; font-size: 14px;">
            <span style="font-weight: 700; color: #1a73e8;">
                Moyenne générale : {{ $data['moyenne_generale'] }} / 20
            </span>
        </div>
        @else
        <p style="font-size: 13px; color: #999;">Aucune note enregistrée pour ce trimestre.</p>
        @endif
    </div>
    @endforeach

    @if($moyenneAnnuelle !== null)
    <div style="border-top: 2px solid #1a73e8; padding-top: 20px; margin-top: 10px; text-align: center;">
        <p style="font-size: 15px; color: #333; margin-bottom: 8px;">
            Moyenne générale annuelle : <strong style="font-size: 18px; color: #1a73e8;">{{ $moyenneAnnuelle }} / 20</strong>
        </p>
        <span style="padding: 8px 20px; border-radius: 20px; font-size: 14px; font-weight: 700; {{ $decision === 'admis' ? 'background:#e6f4ea; color:#2e7d32;' : 'background:#fdecea; color:#c62828;' }}">
            {{ $decision === 'admis' ? '✅ ADMIS(E)' : '❌ REFUSÉ(E)' }}
        </span>
    </div>
    @else
    <div style="border-top: 1px solid #f0f0f0; padding-top: 16px; margin-top: 10px; text-align: center;">
        <p style="font-size: 13px; color: #999;">
            La décision finale sera calculée automatiquement dès que les 3 trimestres auront des notes.
        </p>
    </div>
    @endif

    <div style="margin-top: 24px; text-align: center;">
        <button onclick="window.print()" style="background: #1a73e8; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 14px; cursor: pointer;">
            🖨️ Imprimer
        </button>
    </div>

</div>
</div>
@endsection