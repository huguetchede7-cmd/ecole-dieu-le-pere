@extends('layouts.app')

@section('title', 'Mes épreuves')
@section('page_title', 'Mes épreuves')

@section('content')

@if(session('success'))
<div style="background:#e6f4ea; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
✅ {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background:#fdecea; color:#c62828; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px;">
{{ $errors->first() }}
</div>
@endif

@if(!$classe)
<div style="background:#fdecea; color:#c62828; border-radius:12px; padding:20px; font-size:14px;">
    Aucune classe ne vous est encore affectée. Contactez l'administration.
</div>
@else

<p style="color:#666; font-size:14px; margin-bottom:20px;">
    Classe : <strong>{{ $classe->nom }}</strong>{{ $classe->niveau ? ' (' . $classe->niveau . ')' : '' }}
</p>

@forelse($examens as $e)
@php
    [$libelle, $couleur, $fond] = $e->phase();
    $retard = now('Africa/Porto-Novo')->toDateString() > $e->date_limite_depot->toDateString();
@endphp

<div style="background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden; margin-bottom:24px;">

    <div style="padding:18px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:16px; font-weight:600; color:#333;">{{ $e->titre }}</div>
            <div style="font-size:12px; color:#888; margin-top:4px;">
                {{ $e->type }} · {{ $e->periode }} · dépôt avant le <strong>{{ $e->date_limite_depot->format('d/m/Y') }}</strong>
                · composition du {{ $e->date_debut->format('d/m/Y') }} au {{ $e->date_fin->format('d/m/Y') }}
            </div>
        </div>
        <span style="background:{{ $fond }}; color:{{ $couleur }}; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;">{{ $libelle }}</span>
    </div>

    @if($retard)
    <div style="background:#fff4e5; color:#e65100; padding:10px 20px; font-size:13px;">
        ⚠️ La date limite de dépôt est dépassée. Vous pouvez encore déposer vos épreuves, mais le retard sera noté.
    </div>
    @endif

    @if($e->consignes)
    <div style="background:#f8f9fa; color:#555; padding:10px 20px; font-size:13px;">
        <strong>Consignes :</strong> {{ $e->consignes }}
    </div>
    @endif

    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f8f9fa;">
                <th style="padding:12px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Matière</th>
                <th style="padding:12px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Statut</th>
                <th style="padding:12px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Fichier</th>
                <th style="padding:12px 20px; text-align:left; font-size:13px; color:#666; font-weight:600;">Déposer</th>
            </tr>
        </thead>
        <tbody>
            @forelse($matieres as $m)
            @php $ep = $epreuves->get($e->id . '-' . $m->id); @endphp
            <tr style="border-top:1px solid #f0f0f0;">
                <td style="padding:14px 20px; font-size:14px; color:#333;"><strong>{{ $m->nom }}</strong></td>

                <td style="padding:14px 20px; font-size:13px; color:#555;">
                    @if($ep)
                        <span style="color:#2e7d32; font-weight:600;">✅ Déposée</span><br>
                        <span style="font-size:12px; color:#888;">le {{ $ep->updated_at->timezone('Africa/Porto-Novo')->format('d/m/Y à H:i') }}</span>
                        @if($ep->depose_en_retard)
                            <br><span style="background:#fff4e5; color:#e65100; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:600;">en retard</span>
                        @endif
                    @else
                        <span style="color:#e65100; font-weight:600;">⏳ À déposer</span>
                    @endif
                </td>

                <td style="padding:14px 20px; font-size:13px;">
                    @if($ep)
                        <a href="/admin/epreuves/{{ $ep->id }}/telecharger" style="color:#1a73e8; text-decoration:none;">📄 {{ $ep->nom_original }}</a>
                    @else
                        <span style="color:#bbb;">—</span>
                    @endif
                </td>

                <td style="padding:14px 20px;">
                    <form method="POST" action="/admin/epreuves" enctype="multipart/form-data" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                        @csrf
                        <input type="hidden" name="examen_id" value="{{ $e->id }}">
                        <input type="hidden" name="matiere_id" value="{{ $m->id }}">
                        <input type="file" name="fichier" accept=".pdf,.doc,.docx" required style="font-size:12px; max-width:220px;">
                        <button type="submit" style="background:#1a73e8; color:white; border:none; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                            {{ $ep ? 'Remplacer' : 'Déposer' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="padding:30px; text-align:center; color:#999; font-size:14px;">
                    Aucune matière n'est enregistrée pour le niveau de votre classe. Contactez l'administration.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@empty
<div style="background:white; border-radius:12px; padding:40px; text-align:center; color:#999; font-size:14px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
    Aucune session d'examen en cours pour le moment.
</div>
@endforelse

@endif

@endsection