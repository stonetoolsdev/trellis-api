<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    // Remove assigned_to from tasks
    Schema::table('tasks', function (Blueprint $table) {
      $table->dropForeign(['assigned_to']);
      $table->dropColumn('assigned_to');
    });

    // Create task_assignees pivot table
    Schema::create('task_assignees', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('task_id')->constrained('tasks')->cascadeOnDelete();
      $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
      $table->timestamps();

      $table->unique(['task_id', 'user_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('task_assignees');

    Schema::table('tasks', function (Blueprint $table) {
      $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
    });
  }
};