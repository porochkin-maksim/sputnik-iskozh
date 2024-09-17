<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('polls_questions')) {
            return;
        }

        Schema::create('polls_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('polls')->restrictOnDelete();
            $table->tinyInteger('type');
            $table->text('text');
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls_questions');
    }
};
