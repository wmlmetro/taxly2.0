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
    Schema::create('exchange_invoices', function (Blueprint $table) {
      $table->id();
      $table->string('irn')->unique()->index();
      $table->string('buyer_tin')->index();
      $table->string('seller_tin')->index();
      $table->enum('direction', ['INCOMING', 'OUTGOING'])->index();
      $table->string('status')->index(); // TRANSMITTED, ACKNOWLEDGED, FAILED
      $table->json('invoice_data');
      $table->unsignedBigInteger('tenant_id')->nullable()->index();
      $table->unsignedBigInteger('integrator_id')->nullable()->index();
      $table->timestamp('acknowledged_at')->nullable();
      $table->timestamp('webhook_delivered_at')->nullable();
      $table->timestamps();

      $table->index(['buyer_tin', 'seller_tin']);
      $table->index(['tenant_id', 'integrator_id']);
      $table->index('created_at');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('exchange_invoices');
  }
};
