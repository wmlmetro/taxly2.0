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
        Schema::table('webhook_endpoints', function (Blueprint $table) {
            if (! Schema::hasColumn('webhook_endpoints', 'forwarded_to')) {
                $table->string('forwarded_to')->nullable()->after('url');
            }
            if (! Schema::hasColumn('webhook_endpoints', 'forward_status')) {
                $table->enum('forward_status', ['pending', 'success', 'failed'])
                    ->default('pending')
                    ->after('forwarded_to');
            }
            if (! Schema::hasColumn('webhook_endpoints', 'response_body')) {
                $table->json('response_body')->nullable()->after('forward_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webhook_endpoints', function (Blueprint $table) {
            $table->dropColumn(['forwarded_to', 'forward_status', 'response_body']);
        });
    }
};
