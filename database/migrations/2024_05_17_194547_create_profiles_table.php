<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public const USER_ID     = 'user_id';
    public const FIRST_NAME  = 'first_name';
    public const MIDDLE_NAME = 'middle_name';
    public const LAST_NAME   = 'last_name';
    public const EMAIL       = 'email';
    public const TELEGRAM_ID = 'telegram_id';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('profiles')) {
            return;
        }

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('email')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('telegram_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
