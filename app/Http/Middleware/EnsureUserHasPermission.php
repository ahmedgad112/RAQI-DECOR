<?php

namespace App\Http\Middleware;

use App\Enums\AdminPermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        $required = AdminPermission::tryFrom($permission);

        if (! $user || $required === null || ! $user->hasPermission($required)) {
            abort(403);
        }

        return $next($request);
    }
}
