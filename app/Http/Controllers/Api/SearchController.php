<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
  public function index(Request $request): JsonResponse
  {
    $request->validate([
      'q' => ['required', 'string', 'min:2', 'max:100'],
    ]);

    $query = $request->q;

    $events = Event::where('submission_status', 'approved')
      ->where(function ($q) use ($query) {
        $q->where('title', 'ilike', "%{$query}%")
          ->orWhere('description', 'ilike', "%{$query}%");
      })
      ->select('id', 'title', 'lifecycle_status', 'start_date')
      ->limit(5)
      ->get()
      ->map(fn($e) => [
        'id' => $e->id,
        'title' => $e->title,
        'subtitle' => $e->lifecycle_status,
        'type' => 'event',
        'url' => "/events/{$e->id}",
      ]);

    $projects = Project::where(function ($q) use ($query) {
      $q->where('title', 'ilike', "%{$query}%")
        ->orWhere('description', 'ilike', "%{$query}%");
    })
      ->select('id', 'title', 'status')
      ->limit(5)
      ->get()
      ->map(fn($p) => [
        'id' => $p->id,
        'title' => $p->title,
        'subtitle' => $p->status,
        'type' => 'project',
        'url' => "/projects/{$p->id}",
      ]);

    $tasks = Task::where('user_id', Auth::id())
      ->where(function ($q) use ($query) {
        $q->where('title', 'ilike', "%{$query}%")
          ->orWhere('description', 'ilike', "%{$query}%");
      })
      ->select('id', 'title', 'status', 'priority')
      ->limit(5)
      ->get()
      ->map(fn($t) => [
        'id' => $t->id,
        'title' => $t->title,
        'subtitle' => $t->status,
        'type' => 'task',
        'url' => "/tasks",
      ]);

    $inventory = \App\Models\InventoryItem::where(function ($q) use ($query) {
      $q->where('name', 'ilike', "%{$query}%")
        ->orWhere('category', 'ilike', "%{$query}%")
        ->orWhere('notes', 'ilike', "%{$query}%");
    })
      ->select('id', 'name', 'category', 'stock_status')
      ->limit(5)
      ->get()
      ->map(fn($i) => [
        'id' => $i->id,
        'title' => $i->name,
        'subtitle' => $i->category,
        'type' => 'inventory',
        'url' => "/admin/inventory",
      ]);

    return response()->json([
      'events' => $events,
      'projects' => $projects,
      'tasks' => $tasks,
      'inventory' => $inventory
    ]);
  }
}
