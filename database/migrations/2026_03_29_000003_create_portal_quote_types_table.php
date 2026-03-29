<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_quote_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('portal_id')
                  ->constrained('quote_portals')
                  ->cascadeOnDelete();

            $table->foreignId('quote_type_id')
                  ->constrained('quote_types')
                  ->cascadeOnDelete();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            // Un type ne peut apparaître qu'une fois par portail
            $table->unique(['portal_id', 'quote_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_quote_types');
    }
};
