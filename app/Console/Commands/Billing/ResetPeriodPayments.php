<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPeriodPayments extends Command
{
    protected $signature = 'billing:period:reset-payments
                            {--period= : ID периода для сброса оплат}
                            {--force : Выполнить реальный сброс (без флага — только просмотр)}';

    protected $description = 'Сбрасывает оплаты услуг и схлопывает транзакции в нераспределённое состояние для заданного периода';

    public function handle(): void
    {
        $periodId = (int) $this->option('period');
        $force    = (bool) $this->option('force');

        if ( ! $periodId) {
            $this->error('Необходимо указать --period');

            return;
        }

        $period = DB::table('periods')->find($periodId);
        if ( ! $period) {
            $this->error("Период с ID {$periodId} не найден");

            return;
        }

        $this->info("Период: {$period->name} (ID: {$period->id})");

        $stats = $this->collectStats($periodId);

        $this->line("   Счетов:                 {$stats['invoices']}");
        $this->line("   Услуг с paid > 0:       {$stats['claims_with_paid']}");
        $this->line("   Сумма paid в услугах:   {$stats['total_paid']}");
        $this->line("   Транзакций к отвязке:   {$stats['transactions']}");
        $this->line("   Платежей к схлопыванию: {$stats['payments_count']}");
        $this->line("   Сумма к возврату:       {$stats['transactions_total']}");

        if ( ! $force) {
            $this->warn('DRY-RUN: изменения не сохранены. Запустите с --force для применения.');

            return;
        }

        if ( ! $this->confirm('Сбросить оплаты и отвязать транзакции?', false)) {
            $this->info('Отменено');

            return;
        }

        DB::beginTransaction();
        try {
            // 1. Сбрасываем paid в услугах
            $updatedClaims = DB::table('claims')
                ->join('invoices', 'invoices.id', '=', 'claims.invoice_id')
                ->where('invoices.period_id', $periodId)
                ->update(['claims.paid' => 0])
            ;
            $this->line("Услуг обнулено: {$updatedClaims}");

            // 2. Схлопываем транзакции: собираем суммы по платежам
            $txByPayment = DB::table('payment_transactions')
                ->join('claims', 'claims.id', '=', 'payment_transactions.claim_id')
                ->join('invoices', 'invoices.id', '=', 'claims.invoice_id')
                ->where('invoices.period_id', $periodId)
                ->select([
                    'payment_transactions.payment_id',
                    DB::raw('SUM(payment_transactions.cost) as total'),
                ])
                ->groupBy('payment_transactions.payment_id')
                ->get()
            ;

            // 3. Удаляем старые привязанные транзакции (через ID)
            $txIds = DB::table('payment_transactions')
                ->join('claims', 'claims.id', '=', 'payment_transactions.claim_id')
                ->join('invoices', 'invoices.id', '=', 'claims.invoice_id')
                ->where('invoices.period_id', $periodId)
                ->select('payment_transactions.id')
                ->pluck('id')
            ;

            $deletedCount = DB::table('payment_transactions')
                ->whereIn('id', $txIds)
                ->delete()
            ;
            $this->line("Транзакций удалено: {$deletedCount}");

            // 4. Вставляем одну схлопнутую строку на платёж
            $inserted = 0;
            foreach ($txByPayment as $row) {
                $existing = DB::table('payment_transactions')
                    ->where('payment_id', $row->payment_id)
                    ->whereNull('claim_id')
                    ->first()
                ;

                if ($existing) {
                    DB::table('payment_transactions')
                        ->where('id', $existing->id)
                        ->update(['cost' => DB::raw("cost + {$row->total}")])
                    ;
                }
                else {
                    DB::table('payment_transactions')->insert([
                        'payment_id' => $row->payment_id,
                        'claim_id'   => null,
                        'cost'       => $row->total,
                    ]);
                    $inserted++;
                }
            }
            $this->line("Схлопнуто в {$inserted} нераспределённых транзакций");

            // 5. Пересчитываем paid в счетах
            $invoiceIds = DB::table('invoices')
                ->where('period_id', $periodId)
                ->pluck('id')
            ;

            foreach ($invoiceIds as $invoiceId) {
                $paid = DB::table('claims')
                    ->where('invoice_id', $invoiceId)
                    ->sum('paid')
                ;

                DB::table('invoices')
                    ->where('id', $invoiceId)
                    ->update(['paid' => $paid ? : 0])
                ;
            }
            $this->line("Счетов пересчитано: {$invoiceIds->count()}");

            DB::commit();
            $this->info('Готово.');
        }
        catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Ошибка: ' . $e->getMessage());
            throw $e;
        }
    }

    private function collectStats(int $periodId): array
    {
        $invoices = DB::table('invoices')->where('period_id', $periodId)->count();

        $claimsQuery = DB::table('claims')
            ->join('invoices', 'invoices.id', '=', 'claims.invoice_id')
            ->where('invoices.period_id', $periodId)
        ;

        $claimsWithPaid = (clone $claimsQuery)->where('claims.paid', '>', 0)->count();
        $totalPaid      = (clone $claimsQuery)->sum('claims.paid');

        $txQuery = DB::table('payment_transactions')
            ->join('claims', 'claims.id', '=', 'payment_transactions.claim_id')
            ->join('invoices', 'invoices.id', '=', 'claims.invoice_id')
            ->where('invoices.period_id', $periodId)
        ;

        $transactions      = (clone $txQuery)->count();
        $transactionsTotal = (clone $txQuery)->sum('payment_transactions.cost');

        $paymentsCount = DB::table('payment_transactions')
            ->join('claims', 'claims.id', '=', 'payment_transactions.claim_id')
            ->join('invoices', 'invoices.id', '=', 'claims.invoice_id')
            ->where('invoices.period_id', $periodId)
            ->select(DB::raw('COUNT(DISTINCT payment_transactions.payment_id) as cnt'))
            ->value('cnt')
        ;

        return [
            'invoices'           => $invoices,
            'claims_with_paid'   => $claimsWithPaid,
            'total_paid'         => $totalPaid ? : 0,
            'transactions'       => $transactions,
            'transactions_total' => $transactionsTotal ? : 0,
            'payments_count'     => $paymentsCount,
        ];
    }
}
