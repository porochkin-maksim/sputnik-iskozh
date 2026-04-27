<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasTable('acquiring')) {
            Schema::create('acquiring', static function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->references('id')->on('invoices')->restrictOnDelete();
                $table->foreignId('user_id')->references('id')->on('users')->restrictOnDelete();
                $table->foreignId('payment_id')->nullable()->references('id')->on('payments')->restrictOnDelete();
                $table->unsignedTinyInteger('provider');
                $table->unsignedTinyInteger('status');
                $table->decimal('amount', 20);
                $table->json('data');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acquiring');
    }
};
