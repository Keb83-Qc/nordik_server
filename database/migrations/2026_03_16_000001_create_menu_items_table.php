<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();           // identifiant unique: home, about, services…
            $table->json('label');                     // {fr:'Accueil', en:'Home', es:'Inicio', ht:'Akèy'}
            $table->string('path')->default('');       // chemin sans locale: 'home', 'about', 'contact'
            $table->string('type')->default('link');   // link | mega_services | cta | external
            $table->string('target')->default('_self');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
