<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureRedirect
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    // If this is a redirect response, validate the redirect URL
    $response = $next($request);

    if ($response instanceof \Illuminate\Http\RedirectResponse) {
      $targetUrl = $response->getTargetUrl();

      // Validate that the redirect URL is safe
      if (!$this->isSafeRedirectUrl($targetUrl, $request)) {
        // If unsafe, redirect to a safe default URL
        return redirect()->route('dashboard');
      }
    }

    return $response;
  }

  /**
   * Check if a redirect URL is safe
   *
   * @param  string  $url
   * @param  \Illuminate\Http\Request  $request
   * @return bool
   */
  protected function isSafeRedirectUrl($url, Request $request)
  {
    // Parse the URL
    $parsedUrl = parse_url($url);

    // If no host is specified, it's a relative URL (safe)
    if (!isset($parsedUrl['host'])) {
      return true;
    }

    // Get the application's domain
    $appHost = $request->getHost();

    // Check if the redirect URL is to the same domain
    if ($parsedUrl['host'] === $appHost) {
      return true;
    }

    // Check for subdomains of the main domain
    $appDomainParts = explode('.', $appHost);
    $urlDomainParts = explode('.', $parsedUrl['host']);

    // Compare the last two parts of the domain (e.g., example.com)
    $appDomain = implode('.', array_slice($appDomainParts, -2));
    $urlDomain = implode('.', array_slice($urlDomainParts, -2));

    return $appDomain === $urlDomain;
  }
}
