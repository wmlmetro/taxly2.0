<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // Validate host header to prevent host header poisoning
    $allowedHosts = [
      'dev.taxly.ng',
      'localhost',
      '127.0.0.1',
      'taxly.test',
      'localhost:8000',
      '127.0.0.1:8000'
    ];

    // Check both the resolved host and the explicit Host header (if provided)
    $host = $request->getHost();
    $hostHeader = $request->header('Host');

    if (!in_array($host, $allowedHosts) || ($hostHeader && !in_array($hostHeader, $allowedHosts))) {
      abort(400, 'Invalid host header');
    }

    $response = $next($request);

    // Add security headers
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
    $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

    return $response;
  }
}
