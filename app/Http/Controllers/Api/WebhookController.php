<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Webhook\StoreWebhookEndpointRequest;
use App\Models\WebhookEndpoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WebhookController extends BaseController
{
  use AuthorizesRequests;

  public function index(): JsonResponse
  {
    $orgId = Auth::user()->organization_id;
    return $this->sendResponse(
      WebhookEndpoint::where('organization_id', $orgId)->get(),
      'Webhook endpoints retrieved successfully'
    );
  }

  public function store(StoreWebhookEndpointRequest $req): JsonResponse
  {
    $this->authorize('create', WebhookEndpoint::class);

    $endpoint = WebhookEndpoint::create([
      'organization_id'   => Auth::user()->organization_id,
      'url'               => $req->url,
      'secret'            => $req->secret,
      'subscribed_events' => $req->subscribed_events,
    ]);

    return $this->sendResponse([
      'endpoint' => $endpoint,
    ], 'Webhook endpoint created successfully', 201);
  }

  public function destroy(WebhookEndpoint $webhookEndpoint): JsonResponse
  {
    $this->authorize('delete', $webhookEndpoint);
    $webhookEndpoint->delete();

    return $this->sendResponse([], 'Webhook endpoint deleted successfully');
  }
}
