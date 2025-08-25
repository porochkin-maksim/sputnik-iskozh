<?php declare(strict_types=1);

use App\Http\Resources\Admin\Accounts\AccountsSelectResource;
use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Users\UserResource;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_PROFILE)

@section(SectionNames::CONTENT)
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="mb-2">
                <h3 class="text-dark">{{ lc::userDecorator()->getFullName() }}</h3>
                @if(lc::account()->getId())
                    <h5 class="text-dark d-flex align-items-center"> Участок:
                        @if (lc::user()->getAccounts()->hasMany())
                            <div class="d-inline-block">
                                <account-switcher :accounts='@json(new AccountsSelectResource(lc::user()->getAccounts(), false))'
                                                  :selected='{{ lc::account()->getId() }}'
                                />
                            </div>
                        @else
                            {{ lc::account()->getNumber() }}
                        @endif
                    </h5>
                    <h5 class="text-dark"> Площадь: {{ lc::account()->getSize() }}м²</h5>
                @endif
            </div>
            <div>
                <password-block :account='@json(new AccountResource(lc::account()))'
                                :user='@json(new UserResource(lc::user()))'></password-block>
            </div>
            @if(lc::account()->getId())
                <a class="card mt-2"
                   href="{{ route(RouteNames::PROFILE_COUNTERS) }}">
                    <div class="card-body">
                        {{ RouteNames::name(RouteNames::PROFILE_COUNTERS) }}
                    </div>
                </a>
                <a class="card mt-2"
                   href="{{ route(RouteNames::PROFILE_INVOICES) }}">
                    <div class="card-body">
                        {{ RouteNames::name(RouteNames::PROFILE_INVOICES) }}
                    </div>
                </a>
            @endif
        </div>
    </div>
@endsection