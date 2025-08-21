<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Tenant\TenantRequest;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends BaseController
{
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

    public function store(TenantRequest $request): JsonResponse
    {
        $tenant = Tenant::create($request->validated());

        return $this->sendResponse([
            'tenant' => $tenant,
        ], 'Tenant created successfully', 201);
    }

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
