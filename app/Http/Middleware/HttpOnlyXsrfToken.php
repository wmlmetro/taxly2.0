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

    // Check if XSRF-TOKEN cookie exists and re-set it with HttpOnly flag
    if ($response->headers->has('Set-Cookie')) {
      $cookies = $response->headers->getCookies();
      $response->headers->remove('Set-Cookie');

      foreach ($cookies as $cookie) {
        if ($cookie->getName() === 'XSRF-TOKEN') {
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

    return $response;
  }
}
