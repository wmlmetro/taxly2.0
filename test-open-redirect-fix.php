<?php

// Test script to verify open redirection vulnerability fix

echo "=== Testing Open Redirection Vulnerability Fix ===\n\n";

// Test configuration
$baseUrl = 'http://localhost:8000';
$maliciousReferer = 'https://google.com/';

echo "1. Testing Invoice Email Endpoint with Malicious Referer...\n";
echo "   Target URL: {$baseUrl}/invoices/1/send-email\n";
echo "   Malicious Referer: {$maliciousReferer}\n\n";

// Test the vulnerable endpoint
$ch = curl_init("{$baseUrl}/invoices/1/send-email");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects automatically
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Referer: ' . $maliciousReferer,
  'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
]);

// Add authentication cookie if available
if (file_exists('.test-session')) {
  $sessionData = file_get_contents('.test-session');
  curl_setopt($ch, CURLOPT_COOKIE, $sessionData);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "Response Code: {$httpCode}\n";
echo "Response Headers:\n{$response}\n\n";

// Check if redirect occurred
if ($httpCode >= 300 && $httpCode < 400) {
  echo "⚠️  Redirect detected!\n";

  // Extract Location header
  if (preg_match('/Location:\s*(.*)/i', $response, $matches)) {
    $location = trim($matches[1]);
    echo "Redirect Location: {$location}\n";

    if (strpos($location, 'google.com') !== false) {
      echo "❌ VULNERABILITY STILL EXISTS: Redirecting to external domain!\n";
    } elseif (strpos($location, 'localhost') !== false || strpos($location, '127.0.0.1') !== false) {
      echo "✅ SECURE: Redirecting to local domain only\n";
    } else {
      echo "✅ SECURE: Redirecting to safe location\n";
    }
  }
} else {
  echo "✅ No redirect detected (this is expected for unauthorized requests)\n";
}

echo "\n2. Testing with valid authentication...\n";
echo "   Please ensure you are logged in before running this test.\n";

// Test general redirect behavior
echo "\n3. Testing general redirect security...\n";
$testUrls = [
  '/dashboard',
  '/invoices',
  '/settings'
];

foreach ($testUrls as $url) {
  echo "   Testing: {$baseUrl}{$url}\n";

  $ch = curl_init("{$baseUrl}{$url}");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Referer: ' . $maliciousReferer]);

  if (file_exists('.test-session')) {
    $sessionData = file_get_contents('.test-session');
    curl_setopt($ch, CURLOPT_COOKIE, $sessionData);
  }

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($httpCode >= 300 && $httpCode < 400) {
    if (preg_match('/Location:\s*(.*)/i', $response, $matches)) {
      $location = trim($matches[1]);
      if (strpos($location, 'google.com') !== false) {
        echo "   ❌ VULNERABLE: {$url} redirects to external domain\n";
      } else {
        echo "   ✅ SECURE: {$url} redirects safely\n";
      }
    }
  } else {
    echo "   ✅ SECURE: {$url} does not redirect\n";
  }
}

echo "\n=== Test Complete ===\n";
echo "Summary:\n";
echo "- The fix replaces 'back()' with explicit route redirects\n";
echo "- A SecureRedirect middleware validates all redirect URLs\n";
echo "- External redirects should be blocked or redirected to dashboard\n";
echo "\nTo fully test, ensure you have:\n";
echo "1. A valid user session (save session cookie to .test-session file)\n";
echo "2. At least one invoice in the database\n";
echo "3. The application running on localhost:8000\n";
