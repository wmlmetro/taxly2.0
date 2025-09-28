<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\Auth;

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

    // TODO: You can store webhook events in DB for tracking
    // Example:
    WebhookEndpoint::firstOrCreate([
      'url'     => env('APP_URL') . '/api/webhooks/firs',
      'irn'     => $data['irn'],
      'message' => $data['message'],
    ]);

    // Always return 200 OK
    return response()->json(['status' => 'received'], 200);
  }
}
