<?php declare(strict_types=1);

use App\Resources\RouteNames;

?>
@include('public.partial.requests-grid', [
    'items' => [
        [
            'href' => route(RouteNames::HELP_DESK),
            'title' => RouteNames::name(RouteNames::HELP_DESK),
            'description' => 'Оставить заявку или предложение по поводу улучшения жизни в СНТ'
        ],
        [
            'href' => route(RouteNames::REQUESTS_PAYMENT),
            'title' => RouteNames::name(RouteNames::REQUESTS_PAYMENT),
            'description' => 'Сообщить о платеже'
        ],
        [
            'href' => route(RouteNames::REQUESTS_COUNTER),
            'title' => RouteNames::name(RouteNames::REQUESTS_COUNTER),
            'description' => 'Отправить показания электроэнергии'
        ]
    ]
])
