<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transaction_to_objects')) {
            return;
        }
        Schema::create('transaction_to_objects', static function (Blueprint $table) {
            $table->foreignId('transaction_id')->references('id')->on('transactions')->cascadeOnDelete();
            $table->unsignedTinyInteger('type')->index('typeIndex');
            $table->unsignedBigInteger('reference_id')->index('referenceIdIndex');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_to_objects');
    }
};

