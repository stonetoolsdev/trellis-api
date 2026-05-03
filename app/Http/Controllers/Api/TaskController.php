<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
  public function index(): JsonResponse
  {
    $tasks = Task::where('user_id', Auth::id())
      ->whereNull('event_id')
      ->whereNull('project_id')
      ->with(['assignees', 'subtasks', 'taskList'])
      ->orderBy('priority')
      ->orderBy('due_date')
      ->orderBy('created_at')
      ->get();

    return response()->json($tasks);
  }

  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'title' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'status' => ['sometimes', 'in:incomplete,complete'],
      'priority' => ['nullable', 'in:critical,high,medium,low'],
      'due_date' => ['nullable', 'date'],
      'assignee_ids' => ['nullable', 'array'],
      'assignee_ids.*' => ['exists:users,id'],
      'event_id' => ['nullable', 'exists:events,id'],
      'task_list_id' => ['nullable', 'exists:task_lists,id'],
      'parent_id' => ['nullable', 'exists:tasks,id'],
    ]);

    if ($request->parent_id) {
      $parent = Task::findOrFail($request->parent_id);
      if ($parent->parent_id) {
        return response()->json(['message' => 'Subtasks cannot have subtasks'], 422);
      }
    }

    $task = Task::create([
      'title' => $request->title,
      'description' => $request->description,
      'status' => $request->input('status', TaskStatus::Incomplete->value),
      'priority' => $request->priority,
      'due_date' => $request->due_date,
      'assigned_to' => $request->assigned_to,
      'event_id' => $request->event_id,
      'project_id' => $request->project_id,
      'task_list_id' => $request->task_list_id,
      'parent_id' => $request->parent_id,
      'user_id' => Auth::id(),
    ]);

    if ($request->assignee_ids) {
      $task->assignees()->sync($request->assignee_ids);
    }

    return response()->json($task->load(['assignees', 'subtasks', 'taskList']), 201);
  }

  public function show(Task $task): JsonResponse
  {
    return response()->json($task->load(['assignees', 'subtasks', 'taskList', 'parent']));
  }

  public function update(Request $request, Task $task): JsonResponse
  {
    $request->validate([
      'title' => ['sometimes', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'status' => ['sometimes', 'in:incomplete,complete'],
      'priority' => ['nullable', 'in:critical,high,medium,low'],
      'due_date' => ['nullable', 'date'],
      'assigned_to' => ['nullable', 'exists:users,id'],
      'event_id' => ['nullable', 'exists:events,id'],
      'task_list_id' => ['nullable', 'exists:task_lists,id'],
      'parent_id' => ['nullable', 'exists:tasks,id'],
    ]);

    if ($request->parent_id) {
      $parent = Task::findOrFail($request->parent_id);
      if ($parent->parent_id) {
        return response()->json(['message' => 'Subtasks cannot have subtasks'], 422);
      }
    }

    $task->update($request->only([
      'title',
      'description',
      'status',
      'priority',
      'due_date',
      'assigned_to',
      'event_id',
      'task_list_id',
      'parent_id',
      'project_id'
    ]));

    return response()->json($task->load(['assignees', 'subtasks', 'taskList', 'parent']));
  }

  public function destroy(Task $task): JsonResponse
  {
    $task->delete();

    return response()->json(['message' => 'Task deleted successfully']);
  }
}