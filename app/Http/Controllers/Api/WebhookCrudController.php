<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\WebhookEndpoint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WebhookCrudController extends BaseController
{
  /**
   * List all webhook endpoints
   */
  public function index(): JsonResponse
  {
    $endpoints = WebhookEndpoint::all();

    return response()->json([
      'data' => $endpoints,
      'message' => 'Webhook endpoints retrieved successfully',
    ]);
  }

  /**
   * Create a new webhook endpoint
   */
  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'url' => 'required|url',
      'secret' => 'required|string|min:10',
      'subscribed_events' => 'required|array',
      'subscribed_events.*' => 'string',
    ]);

    // Generate a unique IRN for this webhook endpoint
    $irn = 'WH-' . uniqid();
    $message = 'Webhook endpoint created via API';

    $endpoint = WebhookEndpoint::create([
      'url' => $validated['url'],
      'irn' => $irn,
      'message' => $message,
      'forwarded_to' => $validated['url'],
      'forward_status' => 'pending',
    ]);

    return response()->json([
      'data' => [
        'endpoint' => $endpoint,
      ],
      'message' => 'Webhook endpoint created successfully',
    ], 201);
  }
}
