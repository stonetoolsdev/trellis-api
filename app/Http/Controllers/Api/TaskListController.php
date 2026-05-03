<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TaskList;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskListController extends Controller
{
  public function store(Request $request, Event $event): JsonResponse
  {
    $request->validate([
      'title' => ['required', 'string', 'max:255'],
      'order' => ['sometimes', 'integer'],
    ]);

    $taskList = $event->taskLists()->create([
      'user_id' => Auth::id(),
      'title' => $request->title,
      'order' => $request->input('order', 0),
    ]);

    return response()->json($taskList->load('tasks'), 201);
  }

  public function update(Request $request, Event $event, TaskList $taskList): JsonResponse
  {
    $request->validate([
      'title' => ['sometimes', 'string', 'max:255'],
      'order' => ['sometimes', 'integer'],
    ]);

    $taskList->update($request->only(['title', 'order']));

    return response()->json($taskList->load('tasks'));
  }

  public function destroy(Event $event, TaskList $taskList): JsonResponse
  {
    $taskList->delete();

    return response()->json(['message' => 'Task list deleted successfully']);
  }

  public function storeForProject(Request $request, Project $project): JsonResponse
  {
    $request->validate([
      'title' => ['required', 'string', 'max:255'],
      'order' => ['sometimes', 'integer'],
    ]);

    $taskList = $project->taskLists()->create([
      'user_id' => Auth::id(),
      'title' => $request->title,
      'order' => $request->input('order', 0),
    ]);

    return response()->json($taskList->load('tasks'), 201);
  }
}
