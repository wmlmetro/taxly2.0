<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;

class HttpOnlyXsrfToken
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $response = $next($request);

    // Ensure XSRF-TOKEN cookie is present and has HttpOnly flag
    $foundXsrf = false;
    if ($response->headers->has('Set-Cookie')) {
      $cookies = $response->headers->getCookies();
      $response->headers->remove('Set-Cookie');

      foreach ($cookies as $cookie) {
        if ($cookie->getName() === 'XSRF-TOKEN') {
          $foundXsrf = true;
          // Re-create the cookie with HttpOnly flag
          $newCookie = Cookie::make(
            'XSRF-TOKEN',
            $cookie->getValue(),
            $cookie->getExpiresTime(),
            $cookie->getPath(),
            $cookie->getDomain(),
            $cookie->isSecure(),
            true, // HttpOnly flag set to true
            false, // Raw
            $cookie->getSameSite()
          );
          $response->headers->setCookie($newCookie);
        } else {
          // Keep other cookies as they are
          $response->headers->setCookie($cookie);
        }
      }
    }

    // If no XSRF-TOKEN was set by the framework, create one from the csrf_token()
    if (! $foundXsrf) {
      try {
        $token = function_exists('csrf_token') ? csrf_token() : null;
      } catch (\Throwable $e) {
        $token = null;
      }

      if (! $token) {
        try {
          $token = bin2hex(random_bytes(16));
        } catch (\Throwable $_) {
          $token = (string) now()->timestamp . mt_rand();
        }
      }

      $cookie = Cookie::make('XSRF-TOKEN', $token, 0, '/', null, false, true);
      $response->headers->setCookie($cookie);
    }

    return $response;
  }
}
