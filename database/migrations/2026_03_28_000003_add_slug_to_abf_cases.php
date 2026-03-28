<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abf_cases', function (Blueprint $table) {
            $table->string('slug', 200)->nullable()->after('client_birth_date');
            $table->index(['advisor_user_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('abf_cases', function (Blueprint $table) {
            $table->dropIndex(['advisor_user_id', 'slug']);
            $table->dropColumn('slug');
        });
    }
};
