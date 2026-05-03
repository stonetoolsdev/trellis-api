<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('tasks', function (Blueprint $table) {
      $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
      $table->foreignUuid('task_list_id')->constrained()->onDelete('cascade');
      $table->foreignUuid('assigned_to')->nullable()->constrained('users')->onDelete('set null');
      $table->foreignUuid('created_by')->constrained('users')->onDelete('cascade');
      $table->string('title');
      $table->text('description')->nullable();
      $table->string('status')->default('todo');
      $table->string('priority')->default('medium');
      $table->timestamp('due_date')->nullable();
      $table->integer('sort_order')->default(0);
      $table->timestamp('completed_at')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tasks');
  }
};
