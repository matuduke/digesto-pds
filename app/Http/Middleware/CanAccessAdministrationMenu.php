<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanAccessAdministrationMenu
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $redirectTo = $request->fullUrl();

        if (! $user) {
            $request->session()->put('redirect_to', $redirectTo);

            return redirect()->route('login');
        }

        $hasAccess = $user->groups()
            ->where(function($query) {
                $query->orWhere(Group::NAME, Group::ADMINISTRATORS_GROUP)
                    ->orWhere(Group::NAME, Group::DOCUMENT_CREATORS_GROUP)
                    ->orWhere(Group::NAME, Group::DOCUMENT_APPROVERS_GROUP);

            })
            ->exists();

        if (! $hasAccess) {
            Auth::guard('web')->logout();
            $request->session()->put('redirect_to', '');
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        return $next($request);
    }
}
