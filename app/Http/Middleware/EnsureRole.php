<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowed = array_map(static fn (string $role) => strtolower($role), $roles);
        $currentRole = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        if (! in_array(strtolower($currentRole), $allowed, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Недостаточно прав для доступа.');
        }

        return $next($request);
    }
}
