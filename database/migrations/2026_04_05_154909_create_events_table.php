<?php

use App\Enums\EventFormat;
use App\Enums\LifecycleStatus;
use App\Enums\SubmissionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('events', function (Blueprint $table) {
      $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
      $table->string('title');
      $table->string('slug')->unique();
      $table->text('description')->nullable();
      $table->string('type')->nullable();
      $table->string('format')->default(EventFormat::InPerson->value);
      $table->string('submission_status')->default(SubmissionStatus::Draft->value);
      $table->string('lifecycle_status')->nullable();
      $table->string('location')->nullable();
      $table->string('virtual_url')->nullable();
      $table->timestamp('start_date')->nullable();
      $table->timestamp('end_date')->nullable();
      $table->text('rejection_reason')->nullable();
      $table->foreignUuid('submitted_by')->constrained('users')->onDelete('cascade');
      $table->foreignUuid('approved_by')->nullable()->constrained('users')->onDelete('set null');
      $table->timestamp('approved_at')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('events');
  }
};