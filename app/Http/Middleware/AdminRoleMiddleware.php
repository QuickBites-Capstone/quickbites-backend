<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\AdminRoles;

class AdminRoleMiddleware
{
    
    public function handle(Request $request, Closure $next, string $role)
    {
        if(!Auth::check()){
        return response()->json(['message' => 'Unauthenticated!'], Response::HTTP_UNAUTHORIZED);
    }

    $admin = Auth::user();
    $roleId = $admin->role_id;

    switch ($role){
        case 'admin':
            if($roleId === AdminRoles::Admin->value){
                return $next($request);
            }
            break;

        case 'staff':
            if($roleId === AdminRoles::Staff->value || $roleId === AdminRoles::Admin->value){
                return $next($request);
            }
            break;
    }
    return response()->json(['message' => 'Unauthorized!'], Response::HTTP_FORBIDDEN);
}

}