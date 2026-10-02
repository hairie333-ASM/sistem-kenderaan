<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin has access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        if (! empty($roles) && ! in_array($user->role, $roles)) {
            abort(403, 'Akses tidak dibenarkan bagi peranan akaun anda ('.strtoupper($user->role).'). Sila hubungi Unit Pengurusan Fasiliti (UPF).');
        }

        return $next($request);
    }
}
