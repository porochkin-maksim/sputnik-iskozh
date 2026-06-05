<?php declare(strict_types=1);

use App\Services\Images\StaticFileLocator;

$isIndexPage = $isIndexPage ?? false;

?>

<a class="social-link text-success" target="_blank" href="https://max.ru/join/B6YT7b2T85RTNJ2Nxh8g5D0qU8FYf-1NQEw6JbYVh3Q">
    <div class="logo layout-logo-image rounded-circle-image" style="background-image: url('{{ StaticFileLocator::StaticFileService()->logoSntRed()->getUrl() }}')"></div>
    <div>Основной чат Мах</div>
</a>
<a class="social-link text-success" target="_blank" href="https://chat.whatsapp.com/ET8X52yidq0BmKq9WrKtqb">
    <div class="logo layout-logo-image rounded-circle-image" style="background-image: url('{{ StaticFileLocator::StaticFileService()->logoSnt()->getUrl() }}')"></div>
    <div>Объявления WhatsApp</div>
</a>
<a class="social-link text-success" target="_blank" href="https://chat.whatsapp.com/FqC85651AFAFWuynQbF7UQ">
    <div class="logo layout-logo-image rounded-circle-image" style="background-image: url('{{ StaticFileLocator::StaticFileService()->logoSntOrange()->getUrl() }}')"></div>
    <div>Беседка WhatsApp</div>
</a>
{{--<a class="social-link text-primary" target="_blank" href="https://t.me/SputnikIskozh">--}}
{{--    <i class="fa fa-telegram me-2"></i> Телеграм--}}
{{--</a>--}}
{{--<a class="social-link text-primary" target="_blank" href="https://vk.com/sputnik.iskozh">--}}
{{--    <i class="fa fa-vk me-2"></i> ВК--}}
{{--</a>--}}
