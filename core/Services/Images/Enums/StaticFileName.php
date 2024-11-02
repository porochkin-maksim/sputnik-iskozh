<?php declare(strict_types=1);

namespace Core\Services\Images\Enums;

enum StaticFileName: string
{
    case LOGO_SNT   = 'logo-snt.jpg';
    case REGULATION = 'устав.pdf';

    case BG_SPRING = 'bg/spring.jpeg';
    case BG_SUMMER = 'bg/summer.jpeg';
    case BG_AUTUMN = 'bg/autumn.jpeg';
    case BG_WINTER = 'bg/winter.jpeg';
}
