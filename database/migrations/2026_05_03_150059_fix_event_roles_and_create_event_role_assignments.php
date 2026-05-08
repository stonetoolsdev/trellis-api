<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    // Recreate event_roles with uuid primary key
    Schema::dropIfExists('event_roles');
    Schema::create('event_roles', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('name');
      $table->text('description')->nullable();
      $table->timestamps();
    });

    // Now create event_role_assignments
    Schema::dropIfExists('event_role_assignments');
    Schema::create('event_role_assignments', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('event_id')->constrained('events')->cascadeOnDelete();
      $table->foreignUuid('event_role_id')->constrained('event_roles')->cascadeOnDelete();
      $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
      $table->text('notes')->nullable();
      $table->string('other_description')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('event_role_assignments');
    Schema::dropIfExists('event_roles');
  }
};