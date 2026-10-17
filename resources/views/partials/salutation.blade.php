@php
    $u = \App\Models\Utilisateur::find(session('utilisateur_id'));
    $heure = (int) now('Africa/Porto-Novo')->format('G');
    $salut = $heure < 12 ? 'Bonjour' : ($heure < 18 ? 'Bon après-midi' : 'Bonsoir');
    $appel = trim(($u->civilite ?? '') . ' ' . ($u->nom ?? ''));
@endphp

<div style="background:linear-gradient(135deg,#1a73e8,#4a90e2); color:white; border-radius:12px; padding:24px 28px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
    <h2 style="font-size:22px; font-weight:700; margin:0;">{{ $salut }} {{ $appel }} 👋</h2>
    <p style="margin:6px 0 0; font-size:14px; opacity:0.9;">{{ now('Africa/Porto-Novo')->locale('fr')->translatedFormat('l j F Y') }}</p>
</div>