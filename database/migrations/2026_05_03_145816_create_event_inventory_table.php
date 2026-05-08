<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('event_inventory', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->foreignUuid('event_id')->constrained('events')->cascadeOnDelete();
      $table->foreignUuid('inventory_item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
      $table->integer('quantity_needed')->default(1);
      $table->text('notes')->nullable();
      $table->string('other_description')->nullable(); // for "Other" free-text item
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('event_inventory');
  }
};