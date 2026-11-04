<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAdmin() && ! $user->profileComplete()) {
            return redirect()->route('profile.edit')->with('status', 'Please complete your profile to continue.');
        }

        return $next($request);
    }
}
