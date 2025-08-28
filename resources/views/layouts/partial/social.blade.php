<?php declare(strict_types=1);

use Core\Services\Images\StaticFileLocator;

$isIndexPage = $isIndexPage ?? false;

?>

<a class="social-link text-success" target="_blank" href="https://chat.whatsapp.com/ET8X52yidq0BmKq9WrKtqb">
    <div class="logo" style="background-image: url('{{ StaticFileLocator::StaticFileService()->logoSnt()->getUrl() }}')"></div>
    <div>Объявления</div>
{{--    <i class="fa fa-whatsapp ms-1"></i>--}}
</a>
<a class="social-link text-success" target="_blank" href="https://chat.whatsapp.com/Hfo9oClfdR6BX0dYs7VnG2">
    <div class="logo" style="background-image: url('{{ StaticFileLocator::StaticFileService()->logoSntRed()->getUrl() }}')"></div>
    <div>Основной чат</div>
{{--    <i class="fa fa-whatsapp ms-1 text-success"></i>--}}
</a>
<a class="social-link text-success" target="_blank" href="https://chat.whatsapp.com/FqC85651AFAFWuynQbF7UQ">
    <div class="logo" style="background-image: url('{{ StaticFileLocator::StaticFileService()->logoSntOrange()->getUrl() }}')"></div>
    <div>Беседка</div>
{{--    <i class="fa fa-whatsapp ms-1 text-success"></i>--}}
</a>
<a class="social-link text-primary" target="_blank" href="https://t.me/SputnikIskozh">
    <i class="fa fa-telegram me-2"></i> Телеграм
</a>
<a class="social-link text-primary" target="_blank" href="https://vk.com/sputnik.iskozh">
    <i class="fa fa-vk me-2"></i> ВК
</a>