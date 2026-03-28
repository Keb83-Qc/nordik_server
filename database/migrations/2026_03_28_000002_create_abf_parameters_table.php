<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abf_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50);               // ex: hypotheses, portefeuilles, deces, rrq…
            $table->string('key', 100);                // ex: inflation, prudent, funerailles…
            $table->string('label', 200);              // libellé affiché dans Filament
            $table->string('type', 20)->default('number'); // number, percent, text, select, boolean
            $table->text('value')->nullable();         // valeur stockée en texte
            $table->json('options')->nullable();       // pour les champs de type select
            $table->string('description', 500)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['group', 'key']);
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abf_parameters');
    }
};
