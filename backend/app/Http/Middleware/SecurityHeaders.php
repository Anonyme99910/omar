<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // XSS Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Clickjacking protection
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Content Security Policy (CSP)
        $response->headers->set('Content-Security-Policy', 
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
            "font-src 'self' https://fonts.gstatic.com; " .
            "img-src 'self' data: https:; " .
            "connect-src 'self'; " .
            "frame-ancestors 'self';"
        );
        
        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions Policy
        $response->headers->set('Permissions-Policy', 
            'geolocation=(), microphone=(), camera=()'
        );
        
        // Remove server information
        $response->headers->remove('X-Powered-By');
        $response->headers->set('Server', 'Secure Server');

        return $response;
    }
}
