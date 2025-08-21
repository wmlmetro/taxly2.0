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

use OpenApi\Annotations as OA;

class AuthController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     summary="Register a new tenant, organization, and user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tenant_name","organization_name","name","email","password","password_confirmation"},
     *             @OA\Property(property="tenant_name", type="string", example="Acme Corp"),
     *             @OA\Property(property="organization_name", type="string", example="Head Office"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User and tenant created successfully",
     *         @OA\JsonContent(example={
     *             "message": "User registered successfully",
     *             "success": true,
     *             "data": {
     *                 "user": {
     *                     "id": 1,
     *                     "name": "John Doe",
     *                     "email": "john@example.com",
     *                     "organization_id": 1
     *                 },
     *                 "token": "1|XyzExampleToken..."
     *             }
     *         })
     *     ),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
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

    /**
      @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="Login user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(example={
     *             "message": "User logged in successfully",
     *             "success": true,
     *             "data": {
     *                 "user": {
     *                     "id": 1,
     *                     "name": "John Doe",
     *                     "email": "john@example.com"
     *                 },
     *                 "token": "1|XyzExampleToken..."
     *             }
     *         })
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     summary="Get current authenticated user",
     *     security={{"sanctum":{}}},
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(example={
     *             "message": "User retrieved successfully",
     *             "success": true,
     *             "data": {
     *                 "id": 1,
     *                 "name": "John Doe",
     *                 "email": "john@example.com",
     *                 "organization": {
     *                     "id": 1,
     *                     "legal_name": "Head Office",
     *                     "tenant": {
     *                         "id": 1,
     *                         "name": "Acme Corp"
     *                     }
     *                 }
     *             }
     *         })
     *     )
     * )
     */
    public function me(): JsonResponse
    {
        $user = User::with('organization.tenant')->find(Auth::id());

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->load('organization.tenant');
        return $this->sendResponse($user, 'User retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     summary="Logout user",
     *     security={{"sanctum":{}}},
     *     tags={"Auth"},
     *     @OA\Response(response=200, description="Logged out successfully")
     * )
     */
    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return $this->sendSuccess(['message' => 'Logged out successfully']);
    }
}
