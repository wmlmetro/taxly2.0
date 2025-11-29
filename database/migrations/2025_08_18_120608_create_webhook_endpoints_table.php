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
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable()->index();
            $table->string('url');
            $table->string('irn')->nullable();
            $table->string('message')->nullable();
            $table->string('secret')->nullable();
            $table->json('subscribed_events')->nullable();
            $table->string('forwarded_to')->nullable();
            $table->enum('forward_status', ['pending', 'success', 'failed'])->default('pending');
            $table->json('response_body')->nullable();
            $table->timestamps();

            $table->unique(['irn', 'message']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_endpoints');
    }
};
