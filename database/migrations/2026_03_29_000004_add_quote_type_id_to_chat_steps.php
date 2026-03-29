<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_steps', function (Blueprint $table) {
            $table->foreignId('quote_type_id')
                  ->nullable()
                  ->after('chat_type')
                  ->constrained('quote_types')
                  ->nullOnDelete();
        });

        // Migrer les données existantes : chat_type (string) → quote_type_id (FK)
        // Fonctionne pour: auto, habitation, bundle, commercial
        DB::statement('
            UPDATE chat_steps cs
            JOIN quote_types qt ON qt.slug = cs.chat_type
            SET cs.quote_type_id = qt.id
            WHERE cs.chat_type IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('chat_steps', function (Blueprint $table) {
            $table->dropForeign(['quote_type_id']);
            $table->dropColumn('quote_type_id');
        });
    }
};
