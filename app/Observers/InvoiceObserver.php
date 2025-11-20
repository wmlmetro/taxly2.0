<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\AuditEvent;
use App\Models\UsageMeter;
use Illuminate\Support\Facades\Auth;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        AuditEvent::record(
            "invoice:{$invoice->id}",
            'created',
            Auth::id(),
            $invoice->toArray()
        );

        // Increment invoice counter for the tenant
        if ($invoice->organization && $invoice->organization->tenant) {
            UsageMeter::incrementCounter($invoice->organization->tenant->id, 'invoice_count');
        }
    }

    public function updated(Invoice $invoice): void
    {
        if ($invoice->isDirty('status')) {
            $newStatus = $invoice->status;

            // Map status to audit verbs
            if ($newStatus === 'submitted') {
                AuditEvent::record(
                    "invoice:{$invoice->id}",
                    'submitted',
                    Auth::id(),
                    $invoice->getChanges()
                );

                // Increment submission counter for the tenant
                if ($invoice->organization && $invoice->organization->tenant) {
                    UsageMeter::incrementCounter($invoice->organization->tenant->id, 'submission_count');
                }
            } elseif ($newStatus === 'validated') {
                AuditEvent::record(
                    "invoice:{$invoice->id}",
                    'validated',
                    Auth::id(),
                    $invoice->getChanges()
                );
            } elseif ($newStatus === 'rejected') {
                AuditEvent::record(
                    "invoice:{$invoice->id}",
                    'rejected',
                    Auth::id(),
                    $invoice->getChanges()
                );
            } else {
                // fallback generic update
                AuditEvent::record(
                    "invoice:{$invoice->id}",
                    'updated',
                    Auth::id(),
                    $invoice->getChanges()
                );
            }
        } else {
            // other non-status updates
            AuditEvent::record(
                "invoice:{$invoice->id}",
                'updated',
                Auth::id(),
                $invoice->getChanges()
            );
        }
    }

    public function deleted(Invoice $invoice): void
    {
        AuditEvent::record(
            "invoice:{$invoice->id}",
            'deleted',
            Auth::id(),
            $invoice->toArray()
        );
    }
}
