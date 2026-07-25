<?php declare(strict_types=1);

namespace App\Exports\PaymentsExport;

use App\Exports\PaymentsExport\Sheets\PaymentsSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PaymentsExport implements WithMultipleSheets
{
    private array $headers;

    private const string SHEET_TITLE = 'Платежи';

    public function __construct(
        private readonly array $payments,
    )
    {
        $this->headers = [
            'id'         => 'ID',
            'account'    => 'Участок',
            'cost'       => 'Сумма',
            'paid_at'    => 'Дата платежа',
            'created_at' => 'Дата создания',
            'verified'   => 'Проведён',
            'moderated'  => 'Модерирован',
            'comment'    => 'Комментарий',
        ];
    }

    public function sheets(): array
    {
        return [
            new PaymentsSheet($this->payments, $this->headers, self::SHEET_TITLE),
        ];
    }
}
