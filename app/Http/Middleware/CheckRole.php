<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    // Utilisation : auth.role:admin  ou  auth.role:admin,comptable,directeur
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!session('utilisateur_id')) {
            return redirect('/login');
        }

        if (!in_array(session('utilisateur_role'), $roles, true)) {
            return abort(403, 'Accès non autorisé.');
        }

                // Le directeur peut consulter et modifier, mais jamais supprimer
        if (session('utilisateur_role') === 'directeur' && $request->isMethod('DELETE')) {
            return abort(403, 'Le directeur ne peut pas supprimer.');
        }

        return $next($request);
    }
}