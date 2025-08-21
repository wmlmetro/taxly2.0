<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Tenant\TenantRequest;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/tenants",
     *     summary="List tenants",
     *     tags={"Tenants"},
     *     @OA\Response(response=200, description="List of tenants")
     * )
     */
    public function index(Request $request)
    {
        if ($request->user()->hasRole('super admin')) {
            $tenants = Tenant::all();
        } else {
            $tenants = Tenant::whereHas('organization', function ($q) use ($request) {
                $q->where('id', $request->user()->organization_id);
            })->get();
        }

        return $this->sendResponse($tenants, 'Tenants retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tenants",
     *     summary="Create a tenant",
     *     tags={"Tenants"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Acme Corp"),
     *             @OA\Property(property="brand", type="string", example="Acme"),
     *             @OA\Property(property="domain", type="string", example="acme.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of tenants",
     *         @OA\JsonContent(example={
     *             "message": "Tenants retrieved successfully",
     *             "success": true,
     *             "data": {
     *                 {
     *                     "id": 1,
     *                     "name": "Acme Corp",
     *                     "brand": "Acme",
     *                     "domain": "acme.com"
     *                 }
     *             }
     *         })
     *     )
     * )
     */
    public function store(TenantRequest $request): JsonResponse
    {
        $tenant = Tenant::create($request->validated());

        return $this->sendResponse([
            'tenant' => $tenant,
        ], 'Tenant created successfully', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tenants/{tenant}",
     *     summary="Get tenant details",
     *     tags={"Tenants"},
     *     @OA\Parameter(name="tenant", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Tenant details",
     *         @OA\JsonContent(example={
     *             "message": "Tenant retrieved successfully",
     *             "success": true,
     *             "data": {
     *                 "id": 1,
     *                 "name": "Acme Corp",
     *                 "brand": "Acme",
     *                 "domain": "acme.com"
     *             }
     *         })
     *     )
     * )
     */
    public function show(Tenant $tenant, Request $request)
    {
        if (
            !$request->user()->hasRole('super admin') &&
            $request->user()->organization->tenant_id !== $tenant->id
        ) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $this->sendResponse($tenant, 'Tenant retrieved successfully');
    }
}
