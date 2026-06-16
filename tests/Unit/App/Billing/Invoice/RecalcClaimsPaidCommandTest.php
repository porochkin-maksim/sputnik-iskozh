<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimSearchResponse;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceSearchResponse;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\Billing\Transaction\TransactionCollection;
use Core\Domains\Billing\Transaction\TransactionEntity;
use Core\Domains\Billing\Transaction\TransactionSearcher;
use Core\Domains\Billing\Transaction\TransactionSearchResponse;
use Core\Domains\Billing\Transaction\TransactionService;
use Tests\TestCase;

class RecalcClaimsPaidCommandTest extends TestCase
{
    private InvoiceService          $invoiceService;
    private ClaimService            $claimService;
    private TransactionService      $transactionService;
    private PaymentService          $paymentService;
    private RecalcClaimsPaidCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService     = $this->createMock(InvoiceService::class);
        $this->claimService       = $this->createMock(ClaimService::class);
        $this->transactionService = $this->createMock(TransactionService::class);
        $this->paymentService     = $this->createMock(PaymentService::class);

        $this->command = new RecalcClaimsPaidCommand(
            $this->invoiceService,
            $this->claimService,
            $this->transactionService,
            $this->paymentService,
        );
    }

    public function test_execute_returns_early_when_invoice_not_found(): void
    {
        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($response)
        ;

        $this->claimService->expects($this->never())->method('search');
        $this->invoiceService->expects($this->never())->method('save');

        $this->command->execute(999);
    }

    public function test_execute_saves_invoice_when_no_claims(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(1);

        $invoiceResponse = new InvoiceSearchResponse;
        $invoiceResponse->setItems(new InvoiceCollection([$invoice]));

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($invoiceResponse)
        ;

        $claimResponse = new ClaimSearchResponse;
        $claimResponse->setItems(new ClaimCollection);

        $this->claimService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ClaimSearcher::class))
            ->willReturn($claimResponse)
        ;

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(InvoiceEntity $i) => $i->getCost() === 0.0))
        ;

        $this->command->execute(1);
    }

    public function test_execute_recalculates_cost_from_tariff_times_quantity(): void
    {
        $invoice = (new InvoiceEntity)->setId(1)->setAccountId(10);
        $service = (new ServiceEntity)->setType(ServiceTypeEnum::MEMBERSHIP_FEE);
        $claim   = (new ClaimEntity)
            ->setId(100)->setServiceId(1)->setService($service)
            ->setTariff(1234.56)->setQuantity(1);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$claim])));

        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturn((new TransactionSearchResponse)->setItems(new TransactionCollection));

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->willReturn(new PaymentCollection);

        $savedClaims = null;
        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnCallback(function (ClaimCollection $c) use (&$savedClaims) {
                $savedClaims = $c;
                return $c;
            });

        $savedInvoice = null;
        $this->invoiceService->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (InvoiceEntity $i) use (&$savedInvoice) {
                $savedInvoice = $i;
                return $i;
            });

        $this->command->execute(1);

        $this->assertNotNull($savedClaims);
        $this->assertSame(1234.56, $savedClaims->first()->getCost());
        $this->assertNotNull($savedInvoice);
        $this->assertSame(1235.0, $savedInvoice->getCost());
        $this->assertEqualsWithDelta(0.44, $savedInvoice->getRounding(), 0.0001);
    }

    public function test_execute_deletes_advance_claims_and_keeps_others(): void
    {
        $invoice = (new InvoiceEntity)->setId(1)->setAccountId(10);

        $advance    = (new ServiceEntity)->setType(ServiceTypeEnum::ADVANCE_PAYMENT);
        $membership = (new ServiceEntity)->setType(ServiceTypeEnum::MEMBERSHIP_FEE);

        $advanceClaim  = (new ClaimEntity)->setId(100)->setServiceId(1)->setService($advance)->setTariff(500.0)->setQuantity(1);
        $memberClaim   = (new ClaimEntity)->setId(101)->setServiceId(2)->setService($membership)->setTariff(300.0)->setQuantity(1);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$advanceClaim, $memberClaim])));

        $this->claimService->expects($this->once())
            ->method('deleteById')
            ->with(100);

        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturn((new TransactionSearchResponse)->setItems(new TransactionCollection));

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->willReturn(new PaymentCollection);

        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnArgument(0);

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (InvoiceEntity $i) {
                $this->assertSame(300.0, $i->getCost());
                $this->assertSame(0.0, $i->getDebt());
                return $i;
            });

        $this->command->execute(1);
    }

    public function test_execute_preserves_existing_allocated_transactions(): void
    {
        $invoice = (new InvoiceEntity)->setId(1)->setAccountId(10);
        $service = (new ServiceEntity)->setType(ServiceTypeEnum::MEMBERSHIP_FEE);
        $claim   = (new ClaimEntity)->setId(100)->setServiceId(1)->setService($service)->setTariff(1000.0)->setQuantity(1);

        $existingTx = (new TransactionEntity)->setId(1)->setPaymentId(50)->setClaimId(100)->setCost(600.0);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$claim])));

        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturn((new TransactionSearchResponse)->setItems(new TransactionCollection([$existingTx])));

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->willReturn(new PaymentCollection);

        $savedClaims = null;
        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnCallback(function (ClaimCollection $c) use (&$savedClaims) {
                $savedClaims = $c;
                return $c;
            });

        $this->invoiceService->expects($this->once())
            ->method('save');

        $this->command->execute(1);

        $this->assertNotNull($savedClaims);
        $this->assertSame(600.0, $savedClaims->first()->getPaid());
    }

    public function test_execute_releases_excess_when_tariff_decreases(): void
    {
        $invoice   = (new InvoiceEntity)->setId(1)->setAccountId(10);
        $service   = (new ServiceEntity)->setType(ServiceTypeEnum::MEMBERSHIP_FEE);
        $claim     = (new ClaimEntity)->setId(100)->setServiceId(1)->setService($service)->setTariff(500.0)->setQuantity(1);
        $existingTx = (new TransactionEntity)->setId(1)->setPaymentId(50)->setClaimId(100)->setCost(800.0);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$claim])));

        // Первый search: поиск распределённых транзакций (claimIds=[100])
        // Второй search: поиск транзакций claim для releaseFromClaim (claimIds=[100])
        // Третий search: поиск unallocated для releaseFromClaim (paymentId=50, claimId=null) → пусто
        $searchCalls = 0;
        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturnCallback(function () use ($existingTx, &$searchCalls) {
                $searchCalls++;
                if ($searchCalls <= 2) {
                    return (new TransactionSearchResponse)->setItems(new TransactionCollection([$existingTx]));
                }
                return (new TransactionSearchResponse)->setItems(new TransactionCollection);
            });

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->willReturn(new PaymentCollection);

        $this->transactionService->expects($this->any())
            ->method('save');

        $savedClaims = null;
        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnCallback(function (ClaimCollection $c) use (&$savedClaims) {
                $savedClaims = $c;
                return $c;
            });

        $this->invoiceService->expects($this->once())
            ->method('save');

        $this->command->execute(1);

        $this->assertNotNull($savedClaims);
        $this->assertSame(500.0, $savedClaims->first()->getPaid());
    }

    public function test_execute_distributes_unallocated_transactions(): void
    {
        $accountId = 10;
        $invoice   = (new InvoiceEntity)->setId(1)->setAccountId($accountId);
        $service   = (new ServiceEntity)->setType(ServiceTypeEnum::MEMBERSHIP_FEE);
        $claim     = (new ClaimEntity)->setId(100)->setServiceId(1)->setService($service)->setTariff(1000.0)->setQuantity(1);
        $payment   = (new PaymentEntity)->setId(50);
        $unallocatedTx = (new TransactionEntity)->setId(10)->setPaymentId(50)->setClaimId(null)->setCost(1000.0);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$claim])));

        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturn((new TransactionSearchResponse)->setItems(new TransactionCollection));

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->with($accountId)
            ->willReturn(new PaymentCollection([$payment]));

        $this->transactionService->expects($this->any())
            ->method('getUnallocatedBypaymentIds')
            ->willReturn(new TransactionCollection([$unallocatedTx]));

        // 1 save: $unallocatedTx->setClaimId(100) + save (toPay >= txCost, full consume)
        $this->transactionService->expects($this->exactly(1))
            ->method('save');

        $savedClaims = null;
        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnCallback(function (ClaimCollection $c) use (&$savedClaims) {
                $savedClaims = $c;
                return $c;
            });

        $this->invoiceService->expects($this->once())
            ->method('save');

        $this->command->execute(1);

        $this->assertNotNull($savedClaims);
        $this->assertSame(1000.0, $savedClaims->first()->getPaid());
        $this->assertSame(1000.0, $savedClaims->first()->getCost());
    }

    public function test_execute_calculates_debt_proportional_to_rounded_cost(): void
    {
        $invoice = (new InvoiceEntity)->setId(1)->setAccountId(10);
        $debtSvc = (new ServiceEntity)->setType(ServiceTypeEnum::DEBT);
        $otherSvc = (new ServiceEntity)->setType(ServiceTypeEnum::OTHER);

        $debtClaim  = (new ClaimEntity)->setId(100)->setServiceId(1)->setService($debtSvc)->setTariff(100.50)->setQuantity(1);
        $otherClaim = (new ClaimEntity)->setId(101)->setServiceId(2)->setService($otherSvc)->setTariff(200.25)->setQuantity(1);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$debtClaim, $otherClaim])));

        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturn((new TransactionSearchResponse)->setItems(new TransactionCollection));

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->willReturn(new PaymentCollection);

        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnArgument(0);

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (InvoiceEntity $i) {
                $this->assertSame(301.0, $i->getCost());
                $this->assertEqualsWithDelta(0.25, $i->getRounding(), 0.0001);
                $this->assertEqualsWithDelta(100.58, $i->getDebt(), 0.01);
                return $i;
            });

        $this->command->execute(1);
    }

    public function test_execute_rounds_down_when_cents_below_50(): void
    {
        $invoice = (new InvoiceEntity)->setId(1)->setAccountId(10);
        $service = (new ServiceEntity)->setType(ServiceTypeEnum::OTHER);
        $claim   = (new ClaimEntity)->setId(100)->setServiceId(1)->setService($service)->setTariff(100.20)->setQuantity(1);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$claim])));

        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturn((new TransactionSearchResponse)->setItems(new TransactionCollection));

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->willReturn(new PaymentCollection);

        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnArgument(0);

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (InvoiceEntity $i) {
                $this->assertSame(100.0, $i->getCost());
                $this->assertEqualsWithDelta(-0.20, $i->getRounding(), 0.0001);
                return $i;
            });

        $this->command->execute(1);
    }

    public function test_execute_rounds_up_when_cents_at_50(): void
    {
        $invoice = (new InvoiceEntity)->setId(1)->setAccountId(10);
        $service = (new ServiceEntity)->setType(ServiceTypeEnum::OTHER);
        $claim   = (new ClaimEntity)->setId(100)->setServiceId(1)->setService($service)->setTariff(100.50)->setQuantity(1);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$claim])));

        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturn((new TransactionSearchResponse)->setItems(new TransactionCollection));

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->willReturn(new PaymentCollection);

        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnArgument(0);

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (InvoiceEntity $i) {
                $this->assertSame(101.0, $i->getCost());
                $this->assertSame(0.50, $i->getRounding());
                return $i;
            });

        $this->command->execute(1);
    }

    public function test_execute_defaults_quantity_to_one(): void
    {
        $invoice = (new InvoiceEntity)->setId(1)->setAccountId(10);
        $service = (new ServiceEntity)->setType(ServiceTypeEnum::MEMBERSHIP_FEE);
        $claim   = (new ClaimEntity)->setId(100)->setServiceId(1)->setService($service)->setTariff(2500.0)->setQuantity(0);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$claim])));

        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturn((new TransactionSearchResponse)->setItems(new TransactionCollection));

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->willReturn(new PaymentCollection);

        $savedClaims = null;
        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnCallback(function (ClaimCollection $c) use (&$savedClaims) {
                $savedClaims = $c;
                return $c;
            });

        $this->invoiceService->expects($this->once())
            ->method('save');

        $this->command->execute(1);

        $this->assertNotNull($savedClaims);
        $this->assertSame(1.0, $savedClaims->first()->getQuantity());
        $this->assertSame(2500.0, $savedClaims->first()->getCost());
    }

    public function test_execute_advance_claims_release_transactions_to_unallocated(): void
    {
        $invoice = (new InvoiceEntity)->setId(1)->setAccountId(10);

        $advanceSvc = (new ServiceEntity)->setType(ServiceTypeEnum::ADVANCE_PAYMENT);
        $otherSvc   = (new ServiceEntity)->setType(ServiceTypeEnum::OTHER);

        $advanceClaim = (new ClaimEntity)->setId(100)->setServiceId(1)->setService($advanceSvc)->setTariff(500.0)->setQuantity(1);
        $otherClaim   = (new ClaimEntity)->setId(101)->setServiceId(2)->setService($otherSvc)->setTariff(300.0)->setQuantity(1);

        $tx = (new TransactionEntity)->setId(1)->setPaymentId(50)->setClaimId(100)->setCost(500.0);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$advanceClaim, $otherClaim])));

        $this->claimService->expects($this->once())
            ->method('deleteById')
            ->with(100);

        // search: releaseClaimTransactions → tx for claimId=100
        // search: releaseFromClaim → unallocated with paymentId=50, claimId=null → empty
        // search: main flow → allocated txns for claimIds=[101] → empty
        $searchCalls = 0;
        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturnCallback(function () use ($tx, &$searchCalls) {
                $searchCalls++;
                if ($searchCalls === 1) {
                    return (new TransactionSearchResponse)->setItems(new TransactionCollection([$tx]));
                }
                return (new TransactionSearchResponse)->setItems(new TransactionCollection);
            });

        $this->transactionService->expects($this->any())
            ->method('save');

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->willReturn(new PaymentCollection);

        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnArgument(0);

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (InvoiceEntity $i) {
                $this->assertSame(300.0, $i->getCost());
                $this->assertSame(0.0, $i->getDebt());
                return $i;
            });

        $this->command->execute(1);
    }

    public function test_execute_skips_rounding_for_account_id_1(): void
    {
        $invoice = (new InvoiceEntity)->setId(1)->setAccountId(1);
        $service = (new ServiceEntity)->setType(ServiceTypeEnum::MEMBERSHIP_FEE);
        $claim   = (new ClaimEntity)
            ->setId(100)->setServiceId(1)->setService($service)
            ->setTariff(1234.56)->setQuantity(1);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn((new InvoiceSearchResponse)->setItems(new InvoiceCollection([$invoice])));

        $this->claimService->expects($this->once())
            ->method('search')
            ->willReturn((new ClaimSearchResponse)->setItems(new ClaimCollection([$claim])));

        $this->transactionService->expects($this->any())
            ->method('search')
            ->willReturn((new TransactionSearchResponse)->setItems(new TransactionCollection));

        $this->paymentService->expects($this->any())
            ->method('getVerifiedByAccount')
            ->willReturn(new PaymentCollection);

        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnArgument(0);

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (InvoiceEntity $i) {
                $this->assertSame(1234.56, $i->getCost());
                $this->assertSame(0.0, $i->getRounding());
                return $i;
            });

        $this->command->execute(1);
    }
}
