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
        Schema::create('customer_transmissions', function (Blueprint $table) {
            $table->id();
            $table->string('irn')->unique()->nullable();
            $table->string('supplier_name');
            $table->string('supplier_email');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_transmissions');
    }
};
