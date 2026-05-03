<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskListController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\SettingsController;

Route::prefix('v1')->group(function () {

  Route::post('/register', [AuthController::class, 'register']);
  Route::post('/login', [AuthController::class, 'login']);

  Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Teams
    Route::apiResource('teams', TeamController::class);
    Route::post('teams/{team}/members/{userId}', [TeamController::class, 'addMember']);
    Route::delete('teams/{team}/members/{userId}', [TeamController::class, 'removeMember']);

    // Events
    Route::get('events/mine', [EventController::class, 'mine']);
    Route::get('events/submissions', [EventController::class, 'submissions']);
    Route::apiResource('events', EventController::class);
    Route::post('events/{event}/submit', [EventController::class, 'submit']);
    Route::post('events/{event}/approve', [EventController::class, 'approve']);
    Route::post('events/{event}/reject', [EventController::class, 'reject']);
    Route::post('events/{event}/advance', [EventController::class, 'advanceLifecycle']);

    // Events + Teams
    Route::post('events/{event}/teams', [EventController::class, 'assignTeams']);
    Route::delete('events/{event}/teams', [EventController::class, 'removeTeams']);

    // Teams
    Route::apiResource('tasks', TaskController::class);
    Route::post('events/{event}/task-lists', [TaskListController::class, 'store']);
    Route::put('events/{event}/task-lists/{taskList}', [TaskListController::class, 'update']);
    Route::delete('events/{event}/task-lists/{taskList}', [TaskListController::class, 'destroy']);

    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{project}/task-lists', [TaskListController::class, 'storeForProject']);

    // Comments
    Route::post('{type}/{id}/comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

    // Settings
    Route::put('settings/profile', [SettingsController::class, 'updateProfile']);
    Route::put('settings/password', [SettingsController::class, 'updatePassword']);
    Route::post('settings/avatar', [SettingsController::class, 'updateAvatar']);

  });
});