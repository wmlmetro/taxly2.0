<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Tenant;
use App\Models\User;
use App\Services\FirsApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     *             required={"email","password"},
     *             @OA\Property(property="tenant_name", type="string", example="Acme Corp"),
     *             @OA\Property(property="email", type="string", format="email", example="your firs e-invoice login email"),
     *             @OA\Property(property="password", type="string", format="password", example="your firs e-invoice login password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="your firs e-invoice login password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tenant, organization, and user created successfully",
     *         @OA\JsonContent(example={
     *             "message": "Tenant, organization, and user created successfully",
     *             "success": true,
     *             "data": {
     *                 "tenant": {
     *                     "id": 1,
     *                     "name": "Acme Corp",
     *                     "email": "john@example.com",
     *                     "brand": "Acme Brand",
     *                     "domain": "acme.com",
     *                     "entity_id": "123456"
     *                 },
     *                 "organization": {
     *                     "id": 1,
     *                     "service_id": "svc-001",
     *                     "registration_number": "REG-123456",
     *                     "email": "john@example.com",
     *                     "phone": "+1234567890",
     *                     "street_name": "123 Main St",
     *                     "city_name": "Metropolis",
     *                     "postal_zone": "12345",
     *                     "description": "Main office"
     *                 },
     *                 "user": {
     *                     "id": 1,
     *                     "name": "John Doe",
     *                     "email": "john@example.com"
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
        try {
            return DB::transaction(function () use ($request) {
                if (!empty($request->email) && !empty($request->password)) {
                    $doLogin = app(FirsApiService::class)->login($request->email, $request->password);

                    if (!$doLogin || ($doLogin['code'] ?? 500) != 200) {
                        throw new \Exception('FIRS authentication failed');
                    }

                    $request['entity_id'] = $doLogin['data']['entity_id'] ?? null;
                }

                $tenant = Tenant::create([
                    'name'              => $request->tenant_name ?? 'Tenant ' . rand(000000, 999999),
                    'email'             => $request->email,
                    'password'          => !empty($request->password) ? Hash::make($request->password) : null,
                    'entity_id'         => $request->entity_id ?? null,
                    'brand'             => $request->brand ?? null,
                    'domain'            => $request->domain ?? null,
                    'feature_flags'     => [],
                    'retention_policy'  => "default",
                ]);

                if (!empty($request->entity_id)) {
                    $getEntity = app(FirsApiService::class)->getEntity($request->entity_id);

                    if (!$getEntity || !isset($getEntity['code']) || $getEntity['code'] != 200) {
                        throw new \Exception('Failed to fetch entity details from FIRS');
                    }

                    $request['tin']         = $getEntity['data']['businesses'][0]['tin'] ?? null;
                    $request['trade_name']  = $getEntity['data']['businesses'][0]['name'] ?? null;
                    $request['business_id'] = $getEntity['data']['businesses'][0]['id'] ?? null;
                }

                $org = $tenant->organizations()->create([
                    'tin'                   => $request->tin ?? null,
                    'trade_name'            => $request->trade_name ?? null,
                    'business_id'           => $request->business_id ?? null,
                    'service_id'            => $request->service_id ?? null,
                    'registration_number'   => $request->registration_number ?? null,
                    'email'                 => $request->email ?? null,
                    'phone'                 => $request->phone ?? null,
                    'street_name'           => $request->street_name ?? null,
                    'city_name'             => $request->city_name ?? null,
                    'postal_zone'           => $request->postal_zone ?? null,
                    'description'           => $request->description ?? null,
                ]);

                $user = $org->users()->create([
                    'name'      => $request->trade_name,
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
                    'tenant'       => $tenant,
                    'organization' => $org,
                    'user'         => $userData,
                    'token'        => $token,
                ], 'Tenant, organization, and user created successfully', 201, true);
            });
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="Login user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="your firs e-invoice login email"),
     *             @OA\Property(property="password", type="string", format="password", example="your firs e-invoice login password")
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
