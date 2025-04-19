<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middleware globaux.
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Groupes de middleware.
     */
    protected $middlewareGroups = [
        'web' => [
            // cookies + session + csrf
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            // lier les paramètres de route
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // sanctum : détecte les requêtes stateful et tente l’authentification via session/cookies
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,

            // ⚠️ NE PAS OMetTRE : cookies + session (indispensables pour Sanctum en SPA)
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,

            // limitation de débit et bindings
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Aliases pratiques.
     */
    protected $middlewareAliases = [
        'auth'               => \App\Http\Middleware\Authenticate::class,
        'auth.basic'         => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'       => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers'      => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'                => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'              => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm'   => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'             => \App\Http\Middleware\ValidateSignature::class,
        'throttle'           => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'           => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role'               => \App\Http\Middleware\RoleMiddleware::class,   // ton middleware custom
    ];

    /**
     * Middleware individuels assignables dans les routes.
     */
    protected $routeMiddleware = [
        // on peut garder les mêmes clefs que ci‑dessus pour compatibilité
        'auth'     => \App\Http\Middleware\Authenticate::class,
        'role'     => \App\Http\Middleware\RoleMiddleware::class,
        'can'      => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'    => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];
}
