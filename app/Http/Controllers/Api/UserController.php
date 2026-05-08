<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class UserController extends Controller
{
  public function index(): JsonResponse
  {
    $users = User::with('roles')->orderBy('name')->get();
    return response()->json(UserResource::collection($users));
  }

  public function updateRole(Request $request, User $user): JsonResponse
  {
    $request->validate([
      'role' => ['required', 'in:owner,admin,member,guest'],
    ]);

    $role = Role::where('name', $request->role)->firstOrFail();
    $user->roles()->sync([$role->id]);

    return response()->json(new UserResource($user->load('roles')));
  }

  public function destroy(User $user): JsonResponse
  {
    $user->delete();
    return response()->json(['message' => 'User deleted successfully']);
  }

  public function passwordResetLink(User $user): JsonResponse
  {
    $token = Str::random(64);
    \DB::table('password_reset_tokens')->updateOrInsert(
      ['email' => $user->email],
      ['token' => bcrypt($token), 'created_at' => now()]
    );

    $link = env('FRONTEND_URL', 'http://localhost:3000') . "/reset-password?token={$token}&email={$user->email}";

    return response()->json(['link' => $link]);
  }

  public function show(User $user): JsonResponse
  {
    return response()->json(new UserResource($user->load('roles')));
  }

  public function inviteLink(Request $request): JsonResponse
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'email', 'unique:users,email'],
      'role' => ['required', 'in:owner,admin,member,guest'],
    ]);

    // Create user with random password
    $role = Role::where('name', $request->role)->firstOrFail();
    $token = Str::random(64);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt(Str::random(32)),
      'is_active' => false,
    ]);

    $user->roles()->sync([$role->id]);

    \DB::table('password_reset_tokens')->updateOrInsert(
      ['email' => $user->email],
      ['token' => bcrypt($token), 'created_at' => now()]
    );

    $link = env('FRONTEND_URL', 'http://localhost:3000') . "/accept-invite?token={$token}&email={$user->email}";

    return response()->json([
      'user' => new UserResource($user->load('roles')),
      'link' => $link,
    ]);
  }

  public function profile(User $user): JsonResponse
  {
    return response()->json([
      'id' => $user->id,
      'name' => $user->name,
      'pronouns' => $user->pronouns ?? [],
      'avatar' => $user->avatar,
      'timezone' => $user->timezone,
      'roles' => $user->roles->pluck('name'),
    ]);
  }
}
