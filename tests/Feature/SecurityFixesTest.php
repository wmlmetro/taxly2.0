<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Cookie;

class SecurityFixesTest extends TestCase
{
  /**
   * Test that XSRF-TOKEN cookie has HttpOnly flag set
   */
  public function test_xsrf_token_cookie_has_httponly_flag()
  {
    $response = $this->get('/');

    $cookies = $response->headers->getCookies();
    $xsrfCookie = collect($cookies)->firstWhere('name', 'XSRF-TOKEN');

    $this->assertNotNull($xsrfCookie, 'XSRF-TOKEN cookie should be present');
    $this->assertTrue($xsrfCookie->isHttpOnly(), 'XSRF-TOKEN cookie should have HttpOnly flag');
  }

  /**
   * Test that security headers are present in response
   */
  public function test_security_headers_are_present()
  {
    $response = $this->get('/');

    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-XSS-Protection', '1; mode=block');
    $response->assertHeader('Referrer-Policy', 'no-referrer-when-downgrade');
    $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    $response->assertHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
  }

  /**
   * Test that invalid host headers are rejected
   */
  public function test_invalid_host_header_is_rejected()
  {
    $response = $this->withHeaders([
      'Host' => 'malicious.com'
    ])->get('/');

    $response->assertStatus(400);
  }

  /**
   * Test that valid host headers are accepted
   */
  public function test_valid_host_header_is_accepted()
  {
    $allowedHosts = ['dev.taxly.ng', 'localhost', '127.0.0.1', 'taxly.test'];

    foreach ($allowedHosts as $host) {
      $response = $this->withHeaders([
        'Host' => $host
      ])->get('/');

      $response->assertStatus(200);
    }
  }

  /**
   * Test that POST to robots.txt returns 405
   */
  public function test_post_to_robots_txt_returns_405()
  {
    $response = $this->post('/robots.txt');

    $response->assertStatus(405);
  }

  /**
   * Test that GET to robots.txt works normally
   */
  public function test_get_to_robots_txt_works()
  {
    $response = $this->get('/robots.txt');

    $response->assertStatus(200);
  }
}
