<?php

// Security fixes verification script

echo "=== Taxly Security Fixes Verification ===\n\n";

// Test 1: Check if XSRF-TOKEN cookie has HttpOnly flag
echo "1. Testing XSRF-TOKEN HttpOnly flag...\n";
$ch = curl_init('http://localhost:8000/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (strpos($response, 'XSRF-TOKEN') !== false && strpos($response, 'HttpOnly') !== false) {
  echo "✅ XSRF-TOKEN cookie has HttpOnly flag\n";
} else {
  echo "❌ XSRF-TOKEN cookie missing HttpOnly flag\n";
}

// Test 2: Check security headers
echo "\n2. Testing security headers...\n";
$expectedHeaders = [
  'X-Frame-Options' => 'SAMEORIGIN',
  'X-Content-Type-Options' => 'nosniff',
  'X-XSS-Protection' => '1; mode=block',
  'Referrer-Policy' => 'no-referrer-when-downgrade',
  'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
  'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()'
];

foreach ($expectedHeaders as $header => $expectedValue) {
  if (strpos($response, "$header: $expectedValue") !== false) {
    echo "✅ $header header present\n";
  } else {
    echo "❌ $header header missing or incorrect\n";
  }
}

// Test 3: Test POST to robots.txt (should return 405)
echo "\n3. Testing POST to robots.txt (should return 405)...\n";
$ch = curl_init('http://localhost:8000/robots.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 405) {
  echo "✅ POST to robots.txt correctly returns 405\n";
} else {
  echo "❌ POST to robots.txt returned $httpCode instead of 405\n";
}

// Test 4: Test GET to robots.txt (should return 200)
echo "\n4. Testing GET to robots.txt (should return 200)...\n";
$ch = curl_init('http://localhost:8000/robots.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
  echo "✅ GET to robots.txt correctly returns 200\n";
} else {
  echo "❌ GET to robots.txt returned $httpCode instead of 200\n";
}

// Test 5: Test invalid host header
echo "\n5. Testing invalid host header rejection...\n";
$ch = curl_init('http://localhost:8000/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Host: malicious.com']);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 400 || $httpCode == 444) {
  echo "✅ Invalid host header correctly rejected\n";
} else {
  echo "❌ Invalid host header not properly rejected (returned $httpCode)\n";
}

echo "\n=== Security fixes verification complete ===\n";
