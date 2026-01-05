<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('exchange_events', function (Blueprint $table) {
      $table->id();
      $table->string('irn')->index();
      $table->string('status')->index(); // TRANSMITTING, TRANSMITTED, ACKNOWLEDGED, FAILED
      $table->json('raw_payload');
      $table->timestamp('processed_at')->nullable();
      $table->timestamps();

      $table->index(['irn', 'status']);
      $table->index('created_at');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('exchange_events');
  }
};
