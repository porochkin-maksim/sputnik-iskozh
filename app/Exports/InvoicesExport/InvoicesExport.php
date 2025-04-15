<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport;

use App\Exports\InvoicesExport\Sheets\InvoicesMainSheet;
use App\Exports\InvoicesExport\Sheets\InvoicesPlotSheet;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InvoicesExport implements WithMultipleSheets
{
    private array $headers;
    private array $groupedInvoices = [];

    public function __construct(
        private InvoiceCollection $invoices,
    )
    {
        $headers = [
            'id'      => '№',
            'period'  => 'Период',
            'account' => 'Участок',
            'type'    => 'Тип счёта',
            'cost'    => 'Начислено',
        ];

        $claimHeaders = [];
        foreach ($this->invoices as $invoice) {
            foreach ($invoice->getClaims() as $claim) {
                $claimName = $this->getClaimName($claim);
                if (!in_array($claimName, $headers, true)) {
                    $claimHeaders[$claimName] = $claimName;
                }
            }
        }

        foreach ($claimHeaders as $key => $claimHeader) {
            $headers[$key . 'cost'] = $claimHeader;
        }

        $headers['payed'] = 'Оплачено';

        foreach ($claimHeaders as $key => $claimHeader) {
            $headers[$key . 'payed'] = $claimHeader;
        }

        $headers['delta'] = 'Долг';

        $this->headers = $headers;

        // Группируем счета по участкам
        foreach ($this->invoices as $invoice) {
            $accountNumber = $invoice->getAccount()?->getNumber();
            if ($accountNumber) {
                $plotNumber = $this->extractPlotNumber($accountNumber);
                if (!isset($this->groupedInvoices[$plotNumber])) {
                    $this->groupedInvoices[$plotNumber] = [];
                }
                $this->groupedInvoices[$plotNumber][] = $invoice;
            }
        }
    }

    public function sheets(): array
    {
        $sheets = [];

        // Сортируем все счета по номеру участка
        $sortedInvoices = $this->invoices->toArray();
        usort($sortedInvoices, function($a, $b) {
            $aNumber = $this->normalizePlotNumber($a->getAccount()?->getNumber() ?? '');
            $bNumber = $this->normalizePlotNumber($b->getAccount()?->getNumber() ?? '');
            return strnatcmp($aNumber, $bNumber);
        });

        // Добавляем главный лист
        $sheets[] = new InvoicesMainSheet($sortedInvoices, $this->headers);

        // Сортируем ключи по возрастанию
        ksort($this->groupedInvoices, SORT_NUMERIC);

        // Добавляем листы по участкам
        foreach ($this->groupedInvoices as $plotNumber => $plotInvoices) {
            // Сортируем счета внутри листа по номеру участка
            usort($plotInvoices, function($a, $b) {
                $aNumber = $this->normalizePlotNumber($a->getAccount()?->getNumber() ?? '');
                $bNumber = $this->normalizePlotNumber($b->getAccount()?->getNumber() ?? '');
                return strnatcmp($aNumber, $bNumber);
            });

            $sheets[] = new InvoicesPlotSheet($plotInvoices, $this->headers, $plotNumber);
        }

        return $sheets;
    }

    private function getClaimName(ClaimDTO $claim): ?string
    {
        return $claim->getService()?->getName() ?: $claim->getService()?->getType()?->name();
    }

    private function extractPlotNumber(string $accountNumber): int
    {
        $parts = explode('/', $accountNumber);
        if (empty($parts)) {
            return 0;
        }

        // Берем последнюю часть и оставляем только цифры
        $plotNumber = preg_replace('/[^0-9]/', '', end($parts));
        
        return (int) $plotNumber;
    }

    private function normalizePlotNumber(string $accountNumber): string
    {
        $parts = explode('/', $accountNumber);
        if (empty($parts)) {
            return '0';
        }

        // Берем первую часть (номер дачи)
        $dachaNumber = $parts[0];
        
        // Разбиваем на цифры и буквы
        preg_match('/^(\d+)([А-Яа-я]*)/', $dachaNumber, $matches);
        
        // Формируем строку для сортировки: цифры дополняем нулями, буквы добавляем как есть
        $number = str_pad($matches[1] ?? '0', 5, '0', STR_PAD_LEFT);
        $letter = $matches[2] ?? '';
        
        return $number . $letter;
    }
}
