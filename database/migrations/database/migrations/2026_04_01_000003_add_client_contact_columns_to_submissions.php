<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasClientPhone = Schema::hasColumn('submissions', 'client_phone');
        $hasClientEmail = Schema::hasColumn('submissions', 'client_email');

        Schema::table('submissions', function (Blueprint $table) use ($hasClientPhone, $hasClientEmail) {
            if (!$hasClientPhone) {
                $table->string('client_phone', 50)->nullable()->after('data');
            }

            if (!$hasClientEmail) {
                $table->string('client_email', 255)->nullable()->after('client_phone');
            }
        });
    }

    public function down(): void
    {
        $hasClientPhone = Schema::hasColumn('submissions', 'client_phone');
        $hasClientEmail = Schema::hasColumn('submissions', 'client_email');

        Schema::table('submissions', function (Blueprint $table) use ($hasClientPhone, $hasClientEmail) {
            if ($hasClientEmail) {
                $table->dropColumn('client_email');
            }

            if ($hasClientPhone) {
                $table->dropColumn('client_phone');
            }
        });
    }
};
