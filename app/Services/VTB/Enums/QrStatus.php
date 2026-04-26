<?php declare(strict_types=1);

namespace App\Services\VTB\Enums;

enum QrStatus: string
{
    case STARTED          = 'STARTED';          // QR-код cформирован
    case CONFIRMED        = 'CONFIRMED';        // заказ принят к оплате
    case REJECTED         = 'REJECTED';         // платеж отклонен
    case REJECTED_BY_USER = 'REJECTED_BY_USER'; // платеж отклонен мерчантом
    case ACCEPTED         = 'ACCEPTED';         // заказ оплачен
}
