<?php declare(strict_types=1);

use Core\Resources\RouteNames;

?>
<div class="row requests-block w-100 ms-0">
    <div class="col-lg-4 col-md-6 col-12 pe-md-2 px-0">
        <a class="card request-item d-flex align-items-center justify-content-center p-3"
           href="{{ route(RouteNames::REQUESTS_PROPOSAL) }}">
            <h3>{{ RouteNames::name(RouteNames::REQUESTS_PROPOSAL) }}</h3>
            <div class="text-center">
                Отправить идею или предложение по поводу улучшения жизни в СНТ
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6 col-12 pe-lg-2 pe-md-0 pe-0 px-0">
        <a class="card request-item d-flex align-items-center justify-content-center p-3"
           href="{{ route(RouteNames::REQUESTS_PAYMENT) }}">
            <h3>{{ RouteNames::name(RouteNames::REQUESTS_PAYMENT) }}</h3>
            <div class="text-center">
                Сообщить о платеже
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6 col-12 pe-lg-0 pe-md-2 pe-0 px-0">
        <a class="card request-item d-flex align-items-center justify-content-center p-3"
           href="{{ route(RouteNames::REQUESTS_COUNTER) }}">
            <h3>{{ RouteNames::name(RouteNames::REQUESTS_COUNTER) }}</h3>
            <div class="text-center">
                Отправить показания электроэнергии
            </div>
        </a>
    </div>
</div>
