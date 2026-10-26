@php
    $roleCourant = session('utilisateur_role');
    $moi = session('utilisateur_id');
@endphp

@if(in_array($roleCourant, ['admin', 'directeur'], true))
@php
    $mesCorrections = \App\Models\NoteCorrection::where('modifie_par', $moi);

    // Réponses des enseignants que je n'ai pas encore consultées
    $reponsesQuery = (clone $mesCorrections)->whereIn('statut', ['validee', 'refusee'])->whereNull('vue_par_auteur_le');
    $nbReponses = (clone $reponsesQuery)->count();
    $reponses = (clone $reponsesQuery)
        ->with(['note.eleve', 'note.matiere', 'validateur'])
        ->latest('decide_le')
        ->limit(15)
        ->get()
        ->filter(fn($c) => $c->note && $c->note->eleve);

    // Propositions sans réponse
    $attenteQuery = (clone $mesCorrections)->where('statut', 'en_attente');
    $nbAttente = (clone $attenteQuery)->count();
    $enAttente = (clone $attenteQuery)
        ->with(['note.eleve', 'note.matiere', 'validateur'])
        ->oldest()
        ->limit(5)
        ->get()
        ->filter(fn($c) => $c->note && $c->note->eleve);
@endphp

@if($nbReponses > 0 || $nbAttente > 0)
<div style="background:white; border-radius:12px; padding:22px 26px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid #1a73e8;">

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
        <h3 style="font-size:16px; color:#333; margin:0;">✏️ Corrections de notes</h3>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <span style="background:#e6f4ea; color:#2e7d32; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">{{ $nbReponses }} réponse{{ $nbReponses > 1 ? 's' : '' }} à consulter</span>
            <span style="background:#fff4e5; color:#e65100; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">{{ $nbAttente }} en attente de l'enseignant</span>
        </div>
    </div>

    @if($nbReponses > 0)
    <div style="display:flex; justify-content:space-between; align-items:center; margin:14px 0 6px;">
        <strong style="font-size:14px; color:#333;">Réponses des enseignants</strong>
        <form method="POST" action="{{ route('admin.notes.corrections.vu-toutes') }}">
            @csrf
            <button type="submit" style="background:#f0f0f0; color:#333; border:none; padding:6px 14px; border-radius:6px; font-size:12px; cursor:pointer;">Tout marquer comme vu</button>
        </form>
    </div>

    @foreach($reponses as $c)
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; padding:10px 0; border-top:1px solid #f0f0f0;">
        <div style="font-size:14px; color:#555; line-height:1.7;">
            @if($c->statut === 'validee')
                <span style="color:#2e7d32; font-weight:700;">✅ Confirmée</span>
            @else
                <span style="color:#c62828; font-weight:700;">❌ Refusée</span>
            @endif
            par {{ $c->validateur ? $c->validateur->nom : 'l\'enseignant' }}
            · <strong>{{ $c->note->eleve->nom }} {{ $c->note->eleve->prenom }}</strong>
            · {{ $c->note->matiere->nom ?? '' }} ({{ $c->note->periode }})
            : {{ (float) $c->ancienne_note }}/{{ (float) $c->ancienne_note_max }}
            → <strong>{{ (float) $c->nouvelle_note }}/{{ (float) $c->nouvelle_note_max }}</strong>
            @if($c->statut === 'refusee' && $c->motif_refus)
                <br><span style="font-size:13px; color:#c62828;">Raison : <em>« {{ $c->motif_refus }} »</em></span>
            @endif
            @if($c->decide_le)
                <br><span style="font-size:12px; color:#999;">le {{ $c->decide_le->timezone('Africa/Porto-Novo')->format('d/m/Y à H:i') }}</span>
            @endif
        </div>
        <form method="POST" action="{{ route('admin.notes.corrections.vu', $c->id) }}">
            @csrf
            <button type="submit" style="background:#f0f0f0; color:#333; border:none; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer; white-space:nowrap;">Vu</button>
        </form>
    </div>
    @endforeach

    @if($nbReponses > $reponses->count())
        <div style="font-size:13px; color:#777; margin-top:6px;">… et {{ $nbReponses - $reponses->count() }} autre(s) réponse(s).</div>
    @endif
    @endif

    @if($nbAttente > 0)
    <strong style="display:block; font-size:14px; color:#333; margin:18px 0 6px;">Sans réponse de l'enseignant</strong>
    @foreach($enAttente as $c)
    @php $jours = (int) $c->created_at->diffInDays(now()); @endphp
    <div style="padding:8px 0; border-top:1px solid #f0f0f0; font-size:14px; color:#555;">
        <strong>{{ $c->note->eleve->nom }} {{ $c->note->eleve->prenom }}</strong>
        · {{ $c->note->matiere->nom ?? '' }} ({{ $c->note->periode }})
        : {{ (float) $c->ancienne_note }} → <strong>{{ (float) $c->nouvelle_note }}</strong>
        · attend {{ $c->validateur ? $c->validateur->nom : 'un enseignant' }}
        <span style="color:{{ $jours > 3 ? '#c62828' : '#e65100' }}; font-weight:600;">
            depuis {{ $jours < 1 ? 'aujourd\'hui' : $jours . ' jour' . ($jours > 1 ? 's' : '') }}
        </span>
    </div>
    @endforeach
    @if($nbAttente > $enAttente->count())
        <div style="font-size:13px; color:#777; margin-top:6px;">… et {{ $nbAttente - $enAttente->count() }} autre(s).</div>
    @endif
    @endif

    <a href="/admin/notes/corrections" style="display:inline-block; margin-top:16px; background:#1a73e8; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;">
        Voir tout l'historique →
    </a>
</div>
@endif
@endif
