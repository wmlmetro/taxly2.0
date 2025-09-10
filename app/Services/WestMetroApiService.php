<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WestMetroApiService
{
  protected string $baseUrl;
  protected string $apiKey;
  protected string $secret;

  public function __construct()
  {
    $this->baseUrl = config('services.firs.base_url');
    $this->apiKey = config('services.firs.api_key');
    $this->secret = config('services.firs.secret');
  }

  /**
   * Generic GET request
   */
  public function get(string $endpoint, array $params = [])
  {
    $url = rtrim($this->baseUrl, '/') . '/api/v1/' . ltrim($endpoint, '/');

    $response = Http::get($url, $params);

    if ($response->failed()) {
      throw new \Exception("FIRS API call failed: " . $response->body());
    }

    return $response->json();
  }

  /**
   * Generic POST request
   */
  public function post(string $endpoint, array $payload = [])
  {
    $url = rtrim($this->baseUrl, '/') . '/api/v1/' . ltrim($endpoint, '/');

    $response = Http::post($url, $payload);

    if ($response->failed()) {
      throw new \Exception("FIRS API POST call failed: " . $response->body());
    }

    return $response->json();
  }

  /**
   * Example: Get invoice types
   */
  public function getInvoiceTypes()
  {
    return $this->get('invoice/resources/invoice-types');
  }

  /**
   * 
   */
  public function getPaymentMeans()
  {
    return $this->get('invoice/resources/payment_means');
  }

  public function getTaxCategories()
  {
    return $this->get('invoice/resources/tax-categories');
  }

  public function getTin($tin_number)
  {
    return $this->get("LookupTIN/{$tin_number}");
  }

  public function getEntity($entity_id)
  {
    return $this->get("GetEntity/{$entity_id}");
  }

  /**
   * Validate IRN
   */
  public function validateIrn(string $invoiceRef, string $businessId, string $irn)
  {
    return $this->post('ValidateIRN', [
      'invoice_reference' => $invoiceRef,
      'business_id'       => $businessId,
      'irn'               => $irn,
    ]);
  }

  public function login(string $email, string $password)
  {
    return $this->post('Login', [
      'email' => $email,
      'password' => $password,
    ]);
  }
}
