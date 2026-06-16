<?php declare(strict_types=1);

use App\Models\Billing\Claim;
use App\Models\Billing\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained((new Payment)->getTable())->cascadeOnDelete();
            $table->foreignId('claim_id')->nullable()->constrained((new Claim)->getTable())->nullOnDelete();
            $table->decimal('cost', 20)->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
