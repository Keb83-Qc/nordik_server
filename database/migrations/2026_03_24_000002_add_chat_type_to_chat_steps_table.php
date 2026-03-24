<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_steps', function (Blueprint $table) {
            $table->string('chat_type', 20)->default('auto')->after('identifier');
        });
    }

    public function down(): void
    {
        Schema::table('chat_steps', function (Blueprint $table) {
            $table->dropColumn('chat_type');
        });
    }
};
