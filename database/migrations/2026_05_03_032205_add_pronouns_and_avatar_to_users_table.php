<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
  public function up(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->json('pronouns')->nullable()->after('name');
      $table->string('avatar_path')->nullable()->after('avatar_url');
    });
  }

  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropColumn('pronouns');
      $table->dropColumn('avatar_path');
    });
  }
};