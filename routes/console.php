<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncExchangeInvoicesJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the exchange invoices sync job to run every 15 minutes
Schedule::job(new SyncExchangeInvoicesJob())->everyFifteenMinutes();

// Schedule a daily cleanup job for old exchange events
Artisan::command('exchange:cleanup', function () {
    $this->info('Cleaning up old exchange events...');

    $deleted = \App\Models\ExchangeEvent::where('created_at', '<', now()->subDays(30))->delete();

    $this->info("Deleted {$deleted} old exchange events.");
})->purpose('Clean up exchange events older than 30 days')->daily();

// Schedule a daily retry for failed acknowledgements
Artisan::command('exchange:retry-acknowledgements', function () {
    $this->info('Retrying failed acknowledgements...');

    $failedInvoices = \App\Models\ExchangeInvoice::where('status', \App\Models\ExchangeInvoice::STATUS_TRANSMITTED)
        ->whereNotNull('tenant_id')
        ->whereNotNull('webhook_delivered_at')
        ->whereNull('acknowledged_at')
        ->where('created_at', '>=', now()->subDays(7))
        ->get();

    $retryCount = 0;
    foreach ($failedInvoices as $invoice) {
        try {
            app(\App\Services\FirsAcknowledgementService::class)->retryAcknowledgement($invoice);
            $retryCount++;
        } catch (\Exception $e) {
            $this->error("Failed to retry acknowledgement for invoice {$invoice->irn}: " . $e->getMessage());
        }
    }

    $this->info("Retried acknowledgements for {$retryCount} invoices.");
})->purpose('Retry failed invoice acknowledgements')->daily();
