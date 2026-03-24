<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chat_steps')) {
            return;
        }

        Schema::create('chat_steps', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->text('question');
            $table->string('input_type')->default('text');
            $table->json('options')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_steps');
    }
};
