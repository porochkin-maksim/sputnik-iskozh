<?php

use App\Models\Billing\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( ! Schema::hasColumn('payments', 'payed_at')) {
            Schema::table('payments', static function (Blueprint $table) {
                $table->date('payed_at')->nullable()->after('data');
            });
        }

        Payment::all()->each(function (Payment $model) {
            $model->update(['payed_at' => $model->created_at]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ( ! Schema::hasColumn('payments', 'payed_at')) {
            return;
        }

        Schema::create('payments', static function (Blueprint $table) {
            $table->dropColumn('payed_at');
        });
    }
};
