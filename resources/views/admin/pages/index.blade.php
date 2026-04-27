<?php declare(strict_types=1);

use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

$authRole = lc::roleDecorator();
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::METRICS)
    @include(ViewNames::PARTIAL_METRICS)
@endsection

@section(SectionNames::CONTENT)
    <table class="table">
        <thead>
        <tr>
            <th>Раздел</th>
            <th>Описание</th>
        </tr>
        </thead>
        <tbody>
        @if($authRole->can(PermissionEnum::ROLES_VIEW))
            <tr>
                <th>
                    <a href="{{ route(RouteNames::ADMIN_ROLE_INDEX) }}">
                        {{ RouteNames::name(RouteNames::ADMIN_ROLE_INDEX) }}
                    </a>
                </th>
                <td>
                    Управление ролями доступа в системе
                </td>
            </tr>
        @endif
        @if($authRole->can(PermissionEnum::USERS_VIEW))
            <tr>
                <th>
                    <a href="{{ route(RouteNames::ADMIN_USER_INDEX) }}">
                        {{ RouteNames::name(RouteNames::ADMIN_USER_INDEX) }}
                    </a>
                </th>
                <td>
                    Управление учётными записями
                </td>
            </tr>
        @endif
        @if($authRole->can(PermissionEnum::OPTIONS_VIEW))
            <tr>
                <th>
                    <a href="{{ route(RouteNames::ADMIN_OPTIONS_INDEX) }}">
                        {{ RouteNames::name(RouteNames::ADMIN_OPTIONS_INDEX) }}
                    </a>
                </th>
                <td>
                    Управление опциями сайта
                </td>
            </tr>
        @endif
        @if($authRole->can(PermissionEnum::ACCOUNTS_VIEW))
            <tr>
                <th>
                    <a href="{{ route(RouteNames::ADMIN_ACCOUNT_INDEX) }}">
                        {{ RouteNames::name(RouteNames::ADMIN_ACCOUNT_INDEX) }}
                    </a>
                </th>
                <td>
                    Управление участками - номера, учётные записи. Это основные единицы для системы. Каждый участок - это дачный участок.<br>
                    Так же существует участок с названием <b>"СНТ" (id:1)</b> - это техническая запись для самого СНТ.
                </td>
            </tr>
        @endif
        @if($authRole->can(PermissionEnum::PERIODS_VIEW))
            <tr>
                <th>
                    <a href="{{ route(RouteNames::ADMIN_PERIOD_INDEX) }}">
                        {{ RouteNames::name(RouteNames::ADMIN_PERIOD_INDEX) }}
                    </a>
                </th>
                <td>
                    <div>Управление отчётными календарными циклами.</div>
                    <div>Каждый период - сезонный год жизни СНТ.</div>
                </td>
            </tr>
        @endif
        @if($authRole->can(PermissionEnum::SERVICES_VIEW))
            <tr>
                <th>
                    <a href="{{ route(RouteNames::ADMIN_SERVICE_INDEX) }}">
                        {{ RouteNames::name(RouteNames::ADMIN_SERVICE_INDEX) }}
                    </a>
                </th>
                <td>
                    Управление услугами и их тарифами, такими как:
                    <ul>
                        @foreach(ServiceTypeEnum::names() as $name)
                            <li>{{ $name }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        @endif
        @if($authRole->can(PermissionEnum::INVOICES_VIEW))
            <tr>
                <th>
                    <a href="{{ route(RouteNames::ADMIN_INVOICE_INDEX) }}">
                        {{ RouteNames::name(RouteNames::ADMIN_INVOICE_INDEX) }}
                    </a>
                </th>
                <td>
                    Управление счетами участков, такими как:
                    <ul>
                        <li>
                            <b>{{ InvoiceTypeEnum::REGULAR->name() }}</b> - автоматически содаются транзакции по услугам в привязанном периоде
                            <b class="text-danger">в пользу СНТ</b>
                        </li>
                        <li><b>{{ InvoiceTypeEnum::INCOME->name() }}</b>- счёт для нерегулярных доходов
                            <b class="text-danger">в пользу СНТ</b></li>
                        <li><b>{{ InvoiceTypeEnum::OUTCOME->name() }}</b> - счёт для расходов
                            <b class="text-danger">со счёта СНТ</b></li>
                    </ul>

                    <p>
                        Каждый счёт содержит транзакции - услуги с ценами, например членский взнос и электроэнергия.
                    </p>
                    <p>
                        К счёту можно прикреплять платежи - они автоматически будут распределены по транзакциям в отметку "оплачено".
                        <br>
                        Распределение происходит автоматически сверху вниз. Сначала всегда платеж распределяется на членский взнос и далее на целевые сбор, а после на остальное.<br>
                    </p>
                    <p>
                        Расчёт пунктов "оплачено" для транзакций и счетов всегда рассчитывается автоматически.
                    </p>
                    <p>
                        Все операции по перерасчёту отметок "оплачено" перерасчитываются в фоновом режиме автоматически.
                    </p>
                </td>
            </tr>
        @endif
        @if($authRole->can(PermissionEnum::PAYMENTS_VIEW))
            <tr>
                <th>
                    <a href="{{ route(RouteNames::ADMIN_NEW_PAYMENT_INDEX) }}">
                        {{ RouteNames::name(RouteNames::ADMIN_NEW_PAYMENT_INDEX) }}
                    </a>
                </th>
                <td>
                    Обработка платежей, полученных через
                    <a href="{{ route(RouteNames::REQUESTS_PAYMENT) }}">форму обращений</a>, и привязка их к счетам.
                </td>
            </tr>
        @endif
        @if($authRole->can(PermissionEnum::COUNTERS_VIEW))
            <tr>
                <th>
                    <a href="{{ route(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_INDEX) }}">
                        {{ RouteNames::name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_INDEX) }}
                    </a>
                </th>
                <td>
                    Обработка и привязка показаний счётчиков полученных через
                    <a href="{{ route(RouteNames::REQUESTS_COUNTER) }}">форму обращений</a> и из личного кабинета члена СНТ.<br>
                    При подтверждении показаний для указанного участка будет найден или создан счёт в текущем периоде,
                    к нему будет добавлена транзакция с типом
                    <b>"{{ ServiceTypeEnum::ELECTRIC_TARIFF->name() }}"</b> и рассчитаной стоимостью (Текущий тариф электроэнергии текущего периода * Разницу показаний) .<br>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
@endsection
