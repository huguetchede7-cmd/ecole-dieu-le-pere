@extends('layouts.app')

@section('title', 'Matières')

@section('page_title', 'Gestion des Matières — par niveau')

@section('content')

@if(session('success'))
<div style="background: #e6f4ea; color: #2e7d32; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
    ✅ {{ session('success') }}
</div>
@endif

<p style="color: #666; font-size: 14px; margin-bottom: 24px;">Choisissez un niveau pour voir ou gérer ses matières.</p>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
    @foreach($data as $item)
        @if($item['hasClasse'])
        <a href="/admin/matieres/niveau/{{ rawurlencode($item['niveau']) }}" style="text-decoration: none;">
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: box-shadow 0.2s;">
                <div style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 8px;">{{ $item['niveau'] }}</div>
                <div style="font-size: 13px; color: #1a73e8; font-weight: 600;">{{ $item['count'] }} matière(s)</div>
            </div>
        </a>
        @else
        <div style="background: #f8f9fa; border-radius: 12px; padding: 24px; opacity: 0.5;">
            <div style="font-size: 16px; font-weight: 700; color: #999; margin-bottom: 8px;">{{ $item['niveau'] }}</div>
            <div style="font-size: 13px; color: #999;">Aucune classe créée</div>
        </div>
        @endif
    @endforeach
</div>

@endsection