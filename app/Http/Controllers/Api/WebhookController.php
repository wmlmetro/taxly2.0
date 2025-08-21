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

  /**
   * @OA\Get(
   *     path="/api/v1/webhooks",
   *     summary="List webhook endpoints",
   *     security={{"sanctum":{}}},
   *     tags={"Webhooks"},
   *     @OA\Response(
   *         response=200,
   *         description="Webhook list",
   *         @OA\JsonContent(example={
   *             "message": "Webhooks retrieved successfully",
   *             "success": true,
   *             "data": {
   *                 {
   *                     "id": 1,
   *                     "url": "https://example.com/webhook"
   *                 }
   *             }
   *         })
   *     )
   * )
   */
  public function index(): JsonResponse
  {
    $orgId = Auth::user()->organization_id;
    return $this->sendResponse(
      WebhookEndpoint::where('organization_id', $orgId)->get(),
      'Webhook endpoints retrieved successfully'
    );
  }

  /**
   * @OA\Post(
   *     path="/api/v1/webhooks",
   *     summary="Create a webhook endpoint",
   *     security={{"sanctum":{}}},
   *     tags={"Webhooks"},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"url"},
   *             @OA\Property(property="url", type="string", example="https://example.com/webhook")
   *         )
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Webhook created",
   *         @OA\JsonContent(example={
   *             "message": "Webhook created successfully",
   *             "success": true,
   *             "data": {
   *                 "id": 1,
   *                 "url": "https://example.com/webhook"
   *             }
   *         })
   *     )
   * )
   */
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

  /**
   * @OA\Delete(
   *     path="/api/v1/webhooks/{webhookEndpoint}",
   *     summary="Delete a webhook endpoint",
   *     security={{"sanctum":{}}},
   *     tags={"Webhooks"},
   *     @OA\Parameter(name="webhookEndpoint", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\Response(response=204, description="Webhook deleted")
   * )
   */
  public function destroy(WebhookEndpoint $webhookEndpoint): JsonResponse
  {
    $this->authorize('delete', $webhookEndpoint);
    $webhookEndpoint->delete();

    return $this->sendResponse([], 'Webhook endpoint deleted successfully');
  }
}
