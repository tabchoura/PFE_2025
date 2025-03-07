<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Vérifier si l'utilisateur a le rôle requis
        if ($request->user() && $request->user()->role !== $role) {
            return response()->json(['error' => 'Unauthorized'], 403); // Accès refusé
        }

        return $next($request);
    }
}
