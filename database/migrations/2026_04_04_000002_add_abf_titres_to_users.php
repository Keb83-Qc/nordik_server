<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('abf_titre_fr', 200)->nullable()->after('position');
            $table->string('abf_titre_en', 200)->nullable()->after('abf_titre_fr');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['abf_titre_fr', 'abf_titre_en']);
        });
    }
};
