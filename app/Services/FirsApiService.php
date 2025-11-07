<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirsApiService
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

  protected function withHeaders()
  {
    return Http::withHeaders([
      'x-api-key'    => $this->apiKey,
      'x-api-secret' => $this->secret,
      'Accept'       => 'application/json',
    ]);
  }

  /**
   * Generic GET request
   */
  public function get(string $endpoint, array $params = [])
  {
    $url = rtrim($this->baseUrl, '/') . '/api/v1/' . ltrim($endpoint, '/');

    try {
      $response = $this->withHeaders()->get($url, $params);

      if ($response->failed()) {
        return $response->json() ?? [
          'code' => $response->status(),
          'message' => 'FIRS API call failed',
          'errors'  => $response->body(),
        ];
      }

      return $response->json();
    } catch (\Throwable $e) {
      return [
        'code' => 500,
        'message' => 'Exception during FIRS API call',
        'errors' => $e->getMessage(),
      ];
    }
  }

  /**
   * Generic POST request
   */
  public function post(string $endpoint, array $payload = [])
  {
    $url = rtrim($this->baseUrl, '/') . '/api/v1/' . ltrim($endpoint, '/');

    try {
      $response = $this->withHeaders()->post($url, $payload);

      if ($response->failed()) {
        return $response->json() ?? [
          'code' => $response->status(),
          'message' => 'FIRS API POST call failed',
          'errors'  => $response->body(),
        ];
      }

      return $response->json();
    } catch (\Throwable $e) {
      return [
        'code' => 500,
        'message' => 'Exception during FIRS API POST request',
        'errors' => $e->getMessage(),
      ];
    }
  }

  /**
   * Generic PATCH request
   */
  public function patch(string $endpoint, array $payload = [])
  {
    $url = rtrim($this->baseUrl, '/') . '/api/v1/' . ltrim($endpoint, '/');

    try {
      $response = $this->withHeaders()->patch($url, $payload);

      if ($response->failed()) {
        return $response->json() ?? [
          'code'    => $response->status(),
          'message' => 'FIRS API PATCH call failed',
          'errors'  => $response->body(),
        ];
      }

      return $response->json();
    } catch (\Throwable $e) {
      return [
        'code'    => 500,
        'message' => 'Exception during FIRS API PATCH request',
        'errors'  => $e->getMessage(),
      ];
    }
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
    return $this->get('invoice/resources/payment-means');
  }

  public function getTaxCategories()
  {
    return $this->get('invoice/resources/tax-categories');
  }

  public function getTin($tin_number)
  {
    return $this->get("invoice/transmit/lookup/tin/{$tin_number}");
  }

  public function getEntity($entity_id)
  {
    return $this->get("entity/{$entity_id}");
  }

  /**
   * Confirm the existence of the invoice in FIRS
   */
  public function confirmInvoice($irn)
  {
    return $this->get("invoice/confirm/{$irn}");
  }

  public function download($irn)
  {
    return $this->get("invoice/download/{$irn}");
  }

  public function searchInvoice($business_id)
  {
    return $this->get("invoice/{$business_id}", ["sort_by"]);
  }

  /**
   * Validate IRN
   */
  public function validateIrn(string $invoiceRef, string $businessId, string $irn)
  {
    return $this->post('invoice/irn/validate', [
      'invoice_reference' => $invoiceRef,
      'business_id'       => $businessId,
      'irn'               => $irn,
    ]);
  }

  public function validateInvoice($invoice)
  {
    return $this->post('invoice/validate', $invoice);
  }

  public function invoiceSigning($invoice)
  {
    return $this->post('invoice/sign', $invoice);
  }

  /**
   * Update Invoice via IRN
   */
  public function updateInvoice(string $irn, array $payload)
  {
    return $this->patch("invoice/update/{$irn}", $payload);
  }

  public function login(string $email, string $password)
  {
    return $this->post('utilities/authenticate', [
      'email' => $email,
      'password' => $password,
    ]);
  }

  public function selfHealthCheck()
  {
    return $this->get("invoice/transmit/self-health-check");
  }

  public function transmitInvoice(string $irn)
  {

    return $this->post("invoice/transmit/{$irn}");
  }

  public function getTransmittingInvoice(string $irn)
  {
    return $this->get("invoice/transmit/lookup/{$irn}");
  }

  public function getTransmittedInvoiceByTin(string $tin)
  {
    return $this->get("invoice/transmit/lookup/tin/{$tin}");
  }

  /**
   * Confirm Invoice Transmission via IRN
   */
  public function confirmInvoiceTransmission(string $irn, array $payload)
  {
    return $this->patch("invoice/transmit/{$irn}", $payload);
  }

  public function pullTransmittedInvoices()
  {
    return $this->get("invoice/transmit/pull");
  }
}
