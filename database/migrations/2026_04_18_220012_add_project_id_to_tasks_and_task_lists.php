<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('tasks', function (Blueprint $table) {
      $table->foreignUuid('project_id')->nullable()->constrained('projects')->nullOnDelete()->after('event_id');
    });

    Schema::table('task_lists', function (Blueprint $table) {
      $table->foreignUuid('project_id')->nullable()->constrained('projects')->nullOnDelete()->after('event_id');
    });
  }

  public function down(): void
  {
    Schema::table('tasks', function (Blueprint $table) {
      $table->dropForeign(['project_id']);
      $table->dropColumn('project_id');
    });

    Schema::table('task_lists', function (Blueprint $table) {
      $table->dropForeign(['project_id']);
      $table->dropColumn('project_id');
    });
  }
};