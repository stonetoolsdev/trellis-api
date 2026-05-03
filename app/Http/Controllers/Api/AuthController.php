<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  public function register(RegisterRequest $request): JsonResponse
  {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => $request->password,
      'timezone' => $request->timezone ?? 'UTC',
    ]);

    $memberRole = Role::where('name', 'member')->first();
    if ($memberRole) {
      $user->roles()->attach($memberRole->id);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'user' => new UserResource($user->load('roles')),
      'token' => $token,
    ], 201);
  }

  public function login(LoginRequest $request): JsonResponse
  {
    if (!Auth::attempt($request->only('email', 'password'))) {
      return response()->json([
        'message' => 'Invalid credentials',
      ], 401);
    }

    $user = User::where('email', $request->email)
      ->with('roles')
      ->first();

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'user' => new UserResource($user),
      'token' => $token,
    ]);
  }

  public function logout(): JsonResponse
  {
    Auth::user()->currentAccessToken()->delete();

    return response()->json([
      'message' => 'Logged out successfully',
    ]);
  }

  public function me(): JsonResponse
  {
    return response()->json([
      'user' => new UserResource(Auth::user()->load('roles')),
    ]);
  }
}