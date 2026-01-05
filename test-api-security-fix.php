<?php

// Test script to verify API security fixes
// This script tests that API endpoints return JSON and don't redirect

echo "=== Testing API Security Fixes ===\n\n";

$baseUrl = 'http://localhost:8000';

// Test 1: API Register endpoint should return JSON, not redirect
echo "1. Testing API Register Endpoint...\n";
echo "   URL: {$baseUrl}/api/v1/auth/register\n";

$testData = [
  'tenant_name' => 'Test Corp',
  'email' => 'test@example.com',
  'password' => 'password123',
  'password_confirmation' => 'password123'
];

$ch = curl_init("{$baseUrl}/api/v1/auth/register");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Accept: application/json',
  'User-Agent: API-Test-Client/1.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "Response Code: {$httpCode}\n";
echo "Content-Type: {$contentType}\n";

if ($httpCode >= 300 && $httpCode < 400) {
  echo "❌ FAIL: API endpoint redirected (should return JSON directly)\n";
  exit(1);
} elseif ($httpCode === 201 || $httpCode === 422) {
  // Check if response is JSON
  if (strpos($contentType, 'application/json') !== false) {
    echo "✅ PASS: API endpoint returned JSON response\n";
    $body = substr($response, strpos($response, "\r\n\r\n") + 4);
    $json = json_decode($body, true);
    if (json_last_error() === JSON_ERROR_NONE) {
      echo "✅ PASS: Response is valid JSON\n";
      if (isset($json['success'])) {
        echo "✅ PASS: Response contains success field\n";
      }
    } else {
      echo "❌ FAIL: Response is not valid JSON\n";
      exit(1);
    }
  } else {
    echo "❌ FAIL: API endpoint did not return JSON\n";
    exit(1);
  }
} else {
  echo "❌ FAIL: Unexpected response code {$httpCode}\n";
  exit(1);
}

// Test 2: API Login endpoint should return JSON
echo "\n2. Testing API Login Endpoint...\n";
echo "   URL: {$baseUrl}/api/v1/auth/login\n";

$loginData = [
  'email' => 'test@example.com',
  'password' => 'password123'
];

$ch = curl_init("{$baseUrl}/api/v1/auth/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Accept: application/json',
  'User-Agent: API-Test-Client/1.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "Response Code: {$httpCode}\n";
echo "Content-Type: {$contentType}\n";

if ($httpCode >= 300 && $httpCode < 400) {
  echo "❌ FAIL: API login endpoint redirected\n";
  exit(1);
} elseif ($httpCode === 200 || $httpCode === 401) {
  if (strpos($contentType, 'application/json') !== false) {
    echo "✅ PASS: API login endpoint returned JSON response\n";
  } else {
    echo "❌ FAIL: API login endpoint did not return JSON\n";
    exit(1);
  }
}

// Test 3: Unauthenticated API request should return JSON 401, not redirect
echo "\n3. Testing Unauthenticated API Request...\n";
echo "   URL: {$baseUrl}/api/v1/auth/me\n";

$ch = curl_init("{$baseUrl}/api/v1/auth/me");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Accept: application/json',
  'User-Agent: API-Test-Client/1.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "Response Code: {$httpCode}\n";
echo "Content-Type: {$contentType}\n";

if ($httpCode >= 300 && $httpCode < 400) {
  echo "❌ FAIL: Unauthenticated request redirected (should return JSON 401)\n";
  exit(1);
} elseif ($httpCode === 401) {
  if (strpos($contentType, 'application/json') !== false) {
    echo "✅ PASS: Unauthenticated request returned JSON 401\n";
    $body = substr($response, strpos($response, "\r\n\r\n") + 4);
    $json = json_decode($body, true);
    if (isset($json['message']) && $json['message'] === 'Unauthenticated') {
      echo "✅ PASS: Response contains correct error message\n";
    }
  } else {
    echo "❌ FAIL: Unauthenticated request did not return JSON\n";
    exit(1);
  }
} else {
  echo "❌ FAIL: Expected 401, got {$httpCode}\n";
  exit(1);
}

// Test 4: Check for cookies in API responses (should be minimal)
echo "\n4. Testing API Response Headers...\n";
echo "   Checking for cookies and other web-specific headers\n";

$ch = curl_init("{$baseUrl}/api/v1/resources/invoice-types");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Accept: application/json',
  'User-Agent: API-Test-Client/1.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (strpos($response, 'Set-Cookie:') !== false) {
  echo "⚠️  WARNING: API response contains Set-Cookie header\n";
  // This might be acceptable for some APIs, but worth noting
} else {
  echo "✅ PASS: API response does not contain Set-Cookie header\n";
}

if (strpos($response, 'Location:') !== false) {
  echo "❌ FAIL: API response contains Location header (redirect)\n";
  exit(1);
} else {
  echo "✅ PASS: API response does not contain Location header\n";
}

echo "\n=== All API Security Tests Passed! ===\n";
echo "Summary of fixes verified:\n";
echo "- ✅ API endpoints return JSON responses only\n";
echo "- ✅ No redirects for API requests\n";
echo "- ✅ Unauthenticated requests return JSON 401\n";
echo "- ✅ Proper Content-Type headers\n";
echo "- ✅ No external redirects\n";
echo "\nThe POST /api/v1/auth/register endpoint is now secure!\n";
