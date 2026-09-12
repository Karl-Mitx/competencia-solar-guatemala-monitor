<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAssetManager
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->canManageAssets(), 403, 'Tu rol no permite modificar activos.');

        return $next($request);
    }
}
