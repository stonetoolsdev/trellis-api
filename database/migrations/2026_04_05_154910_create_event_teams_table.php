<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('event_teams', function (Blueprint $table) {
      $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
      $table->foreignUuid('event_id')->constrained()->onDelete('cascade');
      $table->foreignUuid('team_id')->constrained()->onDelete('cascade');
      $table->unique(['event_id', 'team_id']);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('event_teams');
  }
};