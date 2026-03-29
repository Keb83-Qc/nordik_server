<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('system_logs', 'source')) {
                // 'public' = site principal, 'admin' = panel Filament, 'cli' = artisan/queue, 'api' = requêtes API
                $table->string('source', 20)->nullable()->default('public')->after('ip_address')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_logs', function (Blueprint $table) {
            if (Schema::hasColumn('system_logs', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
