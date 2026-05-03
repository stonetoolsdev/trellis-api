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
    Schema::create('task_lists', function (Blueprint $table) {
      $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
      $table->foreignUuid('event_id')->constrained()->onDelete('cascade');
      $table->string('name');
      $table->integer('sort_order')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('task_lists');
  }
};
