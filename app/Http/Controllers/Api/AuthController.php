<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends BaseController
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $tenant = Tenant::create([
            'name'            => $request->tenant_name,
            'brand'           => $request->brand ?? null,
            'domain'          => $request->domain ?? null,
            'feature_flags'   => [],
            'retention_policy' => "default",
        ]);

        $org = $tenant->organizations()->create([
            'tin'        => $request->tin,
            'legal_name' => $request->legal_name,
            'address'    => $request->address,
        ]);

        $user = $org->users()->create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        if (class_exists(Role::class)) {
            $user->assignRole('tenant admin');
        }

        $token = $user->createToken('api')->plainTextToken;

        $userData = $user->toArray();
        unset($userData['password']);

        return $this->sendResponse([
            'tenant' => $tenant,
            'organization' => $org,
            'user' => $userData,
            'token' => $token,
        ], 'Tenant, organization, and user created successfully', 201, true);
    }

    public function login(Request $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        /** @var User $user */
        $user  = Auth::user();
        $token = $user->createToken('api')->plainTextToken;

        return $this->sendResponse([
            'user' => $user->toArray(),
            'token' => $token,
        ], 'User logged in successfully', 200, true);
    }

    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return $this->sendSuccess(['message' => 'Logged out successfully']);
    }

    public function me(): JsonResponse
    {
        $user = User::with('organization.tenant')->find(Auth::id());

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->load('organization.tenant');
        return $this->sendResponse($user, 'User retrieved successfully');
    }
}
