<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!session('utilisateur_id')) {
            return redirect('/login');
        }

        if (session('utilisateur_role') !== $role) {
            return abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}