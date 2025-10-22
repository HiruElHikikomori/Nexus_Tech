<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = Auth::user();

        if(!$user){
            return redirect('/login');
        }

        $userRoleName = $user->role->name ?? null;

        if($userRoleName !== $role){
            abort(403, 'No tines permiso para acceder.');
        }

        return $next($request);
    }
}
