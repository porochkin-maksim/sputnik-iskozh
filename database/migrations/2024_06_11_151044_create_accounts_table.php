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
        if ( ! Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->id();
                $table->string('number')->index()->unique();
                $table->unsignedBigInteger('primary_user_id')->nullable();
                $table->boolean('is_member')->default(false);
                $table->boolean('is_manager')->default(false);
                $table->timestamps();
            });
        }

        if ( ! Schema::hasTable('account_to_user')) {
            Schema::create('account_to_user', function (Blueprint $table) {
                $table->foreignId('account')->references('id')->on('accounts')->cascadeOnDelete();
                $table->foreignId('user')->references('id')->on('users')->cascadeOnDelete();

                $table->primary([
                    'account',
                    'user',
                ]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_to_user');
        Schema::dropIfExists('accounts');
    }
};
