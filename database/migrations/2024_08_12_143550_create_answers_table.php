<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('polls_answers')) {
            return;
        }

        Schema::create('polls_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('polls_questions')->restrictOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls_answers');
    }
};
