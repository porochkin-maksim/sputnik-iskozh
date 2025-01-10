<?php declare(strict_types=1);

namespace Core\Services\Images\Enums;

enum StaticFileName: string
{
    case LOGO_SNT        = 'logo-snt.jpg';
    case LOGO_SNT_ORANGE = 'logo-snt-orange.jpg';
    case LOGO_SNT_RED    = 'logo-snt-red.jpg';
    case QR_PAYMENT      = 'qr-payment.png';
    case REGULATION      = 'устав.pdf';

    case BG_SPRING = 'bg/spring.jpeg';
    case BG_SUMMER = 'bg/summer.jpeg';
    case BG_AUTUMN = 'bg/autumn.jpeg';
    case BG_WINTER = 'bg/winter.jpeg';
}
