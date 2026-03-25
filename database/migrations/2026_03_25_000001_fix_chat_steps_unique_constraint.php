<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_steps', function (Blueprint $table) {
            // Drop the old unique constraint on identifier alone
            $table->dropUnique(['identifier']);

            // Add composite unique constraint: identifier + chat_type
            $table->unique(['identifier', 'chat_type']);
        });
    }

    public function down(): void
    {
        Schema::table('chat_steps', function (Blueprint $table) {
            $table->dropUnique(['identifier', 'chat_type']);
            $table->unique(['identifier']);
        });
    }
};
