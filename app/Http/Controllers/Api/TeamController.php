<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\TeamCollection;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
  public function index(): TeamCollection
  {
    $teams = Team::withCount('members')
      ->with('users')
      ->get();

    return new TeamCollection($teams);
  }

  public function store(StoreTeamRequest $request): JsonResponse
  {
    $team = Team::create($request->validated());

    $team->users()->attach($request->user()->id, ['role' => 'leader']);

    return response()->json(new TeamResource($team->load('users')), 201);
  }

  public function show(Team $team): TeamResource
  {
    return new TeamResource($team->load('users'));
  }

  public function update(UpdateTeamRequest $request, Team $team): TeamResource
  {
    $team->update($request->validated());

    return new TeamResource($team->load('users'));
  }

  public function destroy(Team $team): JsonResponse
  {
    $this->authorize('delete', $team);

    $team->delete();

    return response()->json(['message' => 'Team deleted successfully']);
  }

  public function addMember(Team $team, string $userId): JsonResponse
  {
    $this->authorize('manageMembers', $team);

    $team->users()->syncWithoutDetaching([$userId => ['role' => 'member']]);

    return response()->json(['message' => 'Member added successfully']);
  }

  public function removeMember(Team $team, string $userId): JsonResponse
  {
    $this->authorize('manageMembers', $team);

    $team->users()->detach($userId);

    return response()->json(['message' => 'Member removed successfully']);
  }
}