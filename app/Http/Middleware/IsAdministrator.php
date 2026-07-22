<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $redirectTo = $request->fullUrl();

        if (! $user) {
            $request->session()->put('redirect_to', $redirectTo);

            return redirect()->route('login');
        }

        $isAdministrator = $user->groups()
            ->where(Group::NAME, 'administradores_del_sistema')
            ->exists();

        if (! $isAdministrator) {
            Auth::guard('web')->logout();
            $request->session()->put('redirect_to', $redirectTo);
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        return $next($request);
    }
}
