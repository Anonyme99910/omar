<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        $user = $request->user();
        
        // Admin has all permissions
        if ($user && $user->role === 'admin') {
            return $next($request);
        }
        
        // If no permission required, allow
        if (!$permission) {
            return $next($request);
        }
        
        // Check if user has the required permission
        $userPermissions = $user->permissions ?? [];
        
        if (!in_array($permission, $userPermissions)) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }
        
        return $next($request);
    }
}
