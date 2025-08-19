<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $org = Organization::create([
            'tenant_id'  => $request->tenant_id,
            'tin'        => $request->tin,
            'legal_name' => $request->legal_name,
            'address'    => $request->address,
        ]);

        $user = User::create([
            'organization_id'  => $org->id,
            'name'    => $request->name,
            'email'   => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        $userData = $user->toArray();
        unset($userData['password']);

        return $this->sendResponse([
            'user' => $userData,
            'token' => $token,
        ], 'User registered successfully', 201, true);
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
        return $this->sendResponse(Auth::user(), 'User retrieved successfully');
    }
}
