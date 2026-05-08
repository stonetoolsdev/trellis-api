<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('events', function (Blueprint $table) {
      $table->string('featured_photo_path')->nullable()->after('virtual_url');
    });
  }

  public function down(): void
  {
    Schema::table('events', function (Blueprint $table) {
      $table->dropColumn('featured_photo_path');
    });
  }
};