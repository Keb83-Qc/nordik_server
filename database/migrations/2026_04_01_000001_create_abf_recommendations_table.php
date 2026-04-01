<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abf_recommendations', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['deces', 'invalidite', 'maladie-grave', 'fonds-urgence', 'retraite', 'conseils']);
            $table->string('key', 100)->nullable()->comment('Slug clé (ex: temporaryLifeInsurance, rrq)');
            $table->string('title', 500)->nullable()->comment('Titre affiché (dropdown label + accordion titre)');
            $table->text('text')->comment('Contenu de la recommandation');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('checked_by_default')->default(false)->comment('Conseils: coché par défaut dans l\'accordéon');
            $table->timestamps();

            $table->index(['category', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abf_recommendations');
    }
};
