<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(localized_route('admin.login'));
        }

        $isAdminFlag = (bool) ($user->is_admin ?? false);
        $hasAdminRole = method_exists($user, 'hasRole') && $user->hasRole('admin');

        if (! $isAdminFlag && ! $hasAdminRole) {
            abort(404);
        }

        return $next($request);
    }
}
