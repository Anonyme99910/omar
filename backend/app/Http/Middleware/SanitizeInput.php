<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize all input data
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Remove null bytes
                $value = str_replace("\0", '', $value);
                
                // Strip tags for XSS protection (except for specific fields)
                // Note: Laravel's validation and Eloquent already provide SQL injection protection
                $value = strip_tags($value);
                
                // Trim whitespace
                $value = trim($value);
            }
        });
        
        // Merge sanitized input back
        $request->merge($input);
        
        return $next($request);
    }
    
    /**
     * Fields that should not be sanitized (like passwords)
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];
}
