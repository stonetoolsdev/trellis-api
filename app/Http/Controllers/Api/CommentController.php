<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentMention;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
  public function store(Request $request, string $type, string $id): JsonResponse
  {
    $request->validate([
      'body' => ['required', 'string'],
      'mention_ids' => ['nullable', 'array'],
      'mention_ids.*' => ['exists:users,id'],
    ]);

    $commentable = $this->resolveCommentable($type, $id);

    if (!$commentable) {
      return response()->json(['message' => 'Resource not found'], 404);
    }

    $comment = $commentable->comments()->create([
      'user_id' => Auth::id(),
      'body' => $request->body,
    ]);

    if ($request->mention_ids) {
      foreach ($request->mention_ids as $userId) {
        CommentMention::create([
          'comment_id' => $comment->id,
          'user_id' => $userId,
        ]);
      }
    }

    return response()->json($comment->load(['user', 'mentions.user']), 201);
  }

  public function update(Request $request, Comment $comment): JsonResponse
  {
    $request->validate([
      'body' => ['required', 'string'],
      'mention_ids' => ['nullable', 'array'],
      'mention_ids.*' => ['exists:users,id'],
    ]);

    $comment->update(['body' => $request->body]);

    if ($request->has('mention_ids')) {
      $comment->mentions()->delete();
      foreach ($request->mention_ids as $userId) {
        CommentMention::create([
          'comment_id' => $comment->id,
          'user_id' => $userId,
        ]);
      }
    }

    return response()->json($comment->load(['user', 'mentions.user']));
  }

  public function destroy(Comment $comment): JsonResponse
  {
    $comment->delete();

    return response()->json(['message' => 'Comment deleted successfully']);
  }

  private function resolveCommentable(string $type, string $id)
  {
    return match ($type) {
      'tasks' => \App\Models\Task::find($id),
      'events' => \App\Models\Event::find($id),
      'projects' => \App\Models\Project::find($id),
      default => null,
    };
  }
}