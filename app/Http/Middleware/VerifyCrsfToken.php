<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyCsrfToken
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // "api/setKey"
        "https://secretlab-tech-exercise-production.up.railway.app/api/setKey"
    ];

    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
