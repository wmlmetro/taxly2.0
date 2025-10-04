<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\InvoiceTransmissionMail;
use App\Models\CustomerTransmission;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{
  public function handle(Request $request)
  {
    // Validate payload
    $data = $request->validate([
      'irn'     => 'required|string',
      'message' => 'required|string',
    ]);

    // Log webhook
    Log::info('FIRS Webhook Received', $data);

    WebhookEndpoint::firstOrCreate([
      'url'     => env('APP_URL') . '/api/webhooks/firs',
      'irn'     => $data['irn'],
      'message' => $data['message'],
    ]);

    $transmissionInfo = CustomerTransmission::where('irn', $data['irn'])->first();
    if (!$transmissionInfo) {
      Log::warning('No transmission info found for IRN: ' . $data['irn']);
      return response()->json(['status' => 'no transmission info'], 200);
    }

    // Send email to supplier
    if ($transmissionInfo->supplier_email) {
      Mail::to($transmissionInfo->supplier_email)
        ->queue(new InvoiceTransmissionMail($data['irn'], $transmissionInfo->supplier_name));
    }

    // Send email to customer
    if ($transmissionInfo->customer_email) {
      Mail::to($transmissionInfo->customer_email)
        ->queue(new InvoiceTransmissionMail($data['irn'], $transmissionInfo->customer_name));
    }

    return response()->json([
      'status'  => 'emails sent',
      'message' => 'Invoice updated successfully in FIRS',
    ], 200);
  }
}
