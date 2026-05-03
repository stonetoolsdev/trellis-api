<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void
  {
    Schema::dropIfExists('task_comments');
    Schema::dropIfExists('tasks');
    Schema::dropIfExists('task_lists');

    Schema::create('task_lists', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
      $table->foreignUuid('event_id')->nullable()->constrained('events')->nullOnDelete();
      // $table->foreignUuid('project_id')->nullable()->constrained('projects')->nullOnDelete();
      $table->string('title');
      $table->unsignedInteger('order')->default(0);
      $table->timestamps();
    });

    Schema::create('tasks', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
      $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
      $table->foreignUuid('event_id')->nullable()->constrained('events')->nullOnDelete();
      // $table->foreignUuid('project_id')->nullable()->constrained('projects')->nullOnDelete();
      $table->foreignUuid('task_list_id')->nullable()->constrained('task_lists')->nullOnDelete();
      $table->uuid('parent_id')->nullable();
      $table->string('title');
      $table->text('description')->nullable();
      $table->string('status')->default('incomplete');
      $table->string('priority')->nullable();
      $table->date('due_date')->nullable();
      $table->timestamps();
    });

    DB::statement('ALTER TABLE tasks ADD CONSTRAINT tasks_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES tasks(id) ON DELETE CASCADE');
  }

  public function down(): void
  {
    Schema::dropIfExists('tasks');
    Schema::dropIfExists('task_lists');

    Schema::create('task_lists', function (Blueprint $table) {
      $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
      $table->foreignUuid('event_id')->nullable()->constrained('events')->cascadeOnDelete();
      $table->string('name');
      $table->integer('sort_order')->default(0);
      $table->timestamps();
    });

    Schema::create('tasks', function (Blueprint $table) {
      $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
      $table->foreignUuid('task_list_id')->constrained('task_lists')->cascadeOnDelete();
      $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
      $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
      $table->string('title');
      $table->text('description')->nullable();
      $table->string('status')->default('todo');
      $table->string('priority')->default('medium');
      $table->timestamp('due_date')->nullable();
      $table->integer('sort_order')->default(0);
      $table->timestamp('completed_at')->nullable();
      $table->timestamps();
    });

    Schema::create('task_comments', function (Blueprint $table) {
      $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
      $table->foreignUuid('task_id')->constrained('tasks')->cascadeOnDelete();
      $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
      $table->text('comment');
      $table->timestamps();
    });
  }
};