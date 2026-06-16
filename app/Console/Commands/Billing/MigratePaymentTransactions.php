<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use App\Models\Billing\Payment;
use App\Models\Billing\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MigratePaymentTransactions extends Command
{
    protected $signature   = 'billing:migrate-transactions';
    protected $description = 'Create unallocated transactions for verified payments without transactions';

    public function handle(): void
    {
        $this->info('Creating transactions for verified payments...');

        $payments = Payment::query()
            ->where(Payment::VERIFIED, true)
            ->orderBy(Payment::ID)
            ->get()
        ;

        $bar = $this->output->createProgressBar($payments->count());
        $bar->start();

        $created = 0;

        foreach ($payments as $payment) {
            $exists = Transaction::query()
                ->where(Transaction::PAYMENT_ID, $payment->{Payment::ID})
                ->exists()
            ;

            if ( ! $exists) {
                Transaction::create([
                    Transaction::PAYMENT_ID => $payment->{Payment::ID},
                    Transaction::CLAIM_ID   => null,
                    Transaction::COST       => $payment->{Payment::COST},
                    Transaction::CREATED_AT => Carbon::now(),
                ]);
                $created++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Created {$created} new transactions.");
    }
}
