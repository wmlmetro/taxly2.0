<?php

namespace App\Services;

use App\Models\ExchangeInvoice;
use App\Models\Tenant;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;

class TenantResolverService
{
  /**
   * Resolve which tenant and integrator owns the invoice
   *
   * @param ExchangeInvoice $exchangeInvoice
   * @return array
   */
  public function resolveTenantAndIntegrator(ExchangeInvoice $exchangeInvoice): array
  {
    Log::info('Resolving tenant and integrator for invoice', [
      'invoice_id' => $exchangeInvoice->id,
      'irn' => $exchangeInvoice->irn,
      'buyer_tin' => $exchangeInvoice->buyer_tin,
      'seller_tin' => $exchangeInvoice->seller_tin,
      'direction' => $exchangeInvoice->direction,
    ]);

    try {
      // Try to match by buyer TIN first (for incoming invoices)
      if ($exchangeInvoice->direction === ExchangeInvoice::DIRECTION_INCOMING) {
        $organization = Organization::where('tin', $exchangeInvoice->buyer_tin)->first();

        if ($organization) {
          $tenant = $organization->tenant;

          Log::info('Tenant found by buyer TIN via Organization', [
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tin' => $organization->tin,
          ]);

          return [
            'success' => true,
            'tenant_id' => $tenant->id,
            'integrator_id' => $tenant->id, // Assuming tenant is also the integrator
          ];
        }
      }

      // Try to match by seller TIN (for outgoing invoices)
      if ($exchangeInvoice->direction === ExchangeInvoice::DIRECTION_OUTGOING) {
        $organization = Organization::where('tin', $exchangeInvoice->seller_tin)->first();

        if ($organization) {
          $tenant = $organization->tenant;

          Log::info('Tenant found by seller TIN via Organization', [
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tin' => $organization->tin,
          ]);

          return [
            'success' => true,
            'tenant_id' => $tenant->id,
            'integrator_id' => $tenant->id, // Assuming tenant is also the integrator
          ];
        }
      }

      // Try reverse matching - check if any organization has the other TIN
      $organization = Organization::where(function ($query) use ($exchangeInvoice) {
        $query->where('tin', $exchangeInvoice->buyer_tin)
          ->orWhere('tin', $exchangeInvoice->seller_tin);
      })->first();

      if ($organization) {
        $tenant = $organization->tenant;

        Log::info('Tenant found by reverse TIN matching via Organization', [
          'tenant_id' => $tenant->id,
          'organization_id' => $organization->id,
          'tin' => $organization->tin,
        ]);

        return [
          'success' => true,
          'tenant_id' => $tenant->id,
          'integrator_id' => $tenant->id,
        ];
      }

      // No organization found
      Log::warning('No organization found for invoice TINs', [
        'invoice_id' => $exchangeInvoice->id,
        'buyer_tin' => $exchangeInvoice->buyer_tin,
        'seller_tin' => $exchangeInvoice->seller_tin,
      ]);

      return [
        'success' => false,
        'error' => 'No organization found matching buyer or seller TIN',
      ];
    } catch (\Exception $e) {
      Log::error('Tenant resolution failed', [
        'invoice_id' => $exchangeInvoice->id,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      return [
        'success' => false,
        'error' => 'Tenant resolution failed: ' . $e->getMessage(),
      ];
    }
  }

  /**
   * Retry resolution for unassigned invoices
   * This can be called by scheduled jobs to retry failed resolutions
   *
   * @param ExchangeInvoice $exchangeInvoice
   * @return array
   */
  public function retryResolution(ExchangeInvoice $exchangeInvoice): array
  {
    Log::info('Retrying tenant resolution for unassigned invoice', [
      'invoice_id' => $exchangeInvoice->id,
      'irn' => $exchangeInvoice->irn,
    ]);

    // Re-run the resolution logic
    return $this->resolveTenantAndIntegrator($exchangeInvoice);
  }
}
