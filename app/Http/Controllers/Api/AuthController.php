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
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

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

  public function acceptInvite(Request $request): JsonResponse
  {
    $request->validate([
      'token' => ['required', 'string'],
      'email' => ['required', 'email'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $record = \DB::table('password_reset_tokens')
      ->where('email', $request->email)
      ->first();

    if (!$record || !Hash::check($request->token, $record->token)) {
      return response()->json(['message' => 'Invalid or expired link'], 422);
    }

    $user = User::where('email', $request->email)->firstOrFail();
    $user->update([
      'password' => $request->password,
      'is_active' => true,
    ]);

    \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return response()->json(['message' => 'Account activated successfully']);
  }

  public function resetPassword(Request $request): JsonResponse
  {
    $request->validate([
      'token' => ['required', 'string'],
      'email' => ['required', 'email'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $record = \DB::table('password_reset_tokens')
      ->where('email', $request->email)
      ->first();

    if (!$record || !Hash::check($request->token, $record->token)) {
      return response()->json(['message' => 'Invalid or expired link'], 422);
    }

    $user = User::where('email', $request->email)->firstOrFail();
    $user->update(['password' => $request->password]);

    \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return response()->json(['message' => 'Password reset successfully']);
  }
}