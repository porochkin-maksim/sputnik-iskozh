<?php declare(strict_types=1);

use App\Http\Resources\Common\AccountsSelectResource;
use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Users\UserResource;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

?>

@extends('layouts.profile-layout')

@section(SectionNames::CONTENT)
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="page-hero">
                <h3 class="page-hero__title text-dark">{{ lc::userDecorator()->getFullName() }}</h3>
                <div class="page-hero__lead">
                    Личный кабинет
                </div>
            </div>
            @if(lc::account()->getId())
                <div class="page-card p-3 profile-summary-card mb-3">
                    <div class="profile-summary-card__row">
                        <div class="profile-summary-card__label">Участок</div>
                        <div class="profile-summary-card__value">
                            @if (lc::user()->getAccounts()->hasSeveral())
                                <account-switcher
                                        :accounts='@json(new AccountsSelectResource(lc::user()->getAccounts(), false))'
                                        :selected='{{ lc::account()->getId() }}'
                                ></account-switcher>
                            @else
                                {{ lc::account()->getNumber() }}
                            @endif
                        </div>
                    </div>
                    <div class="profile-summary-card__row">
                        <div class="profile-summary-card__label">Площадь</div>
                        <div class="profile-summary-card__value">{{ lc::account()->getSize() }}м²</div>
                    </div>
                </div>
            @endif
            @if(lc::user()->isRealEmail())
                <div class="page-section profile-password-wrapper">
                    <password-block :account='@json(new AccountResource(lc::account()))'
                                    :user='@json(new UserResource(lc::user()))'
                    ></password-block>
                </div>
            @endif
            @if(lc::account()->getId())
                <a class="card mt-2 page-card text-decoration-none profile-quick-link"
                   href="{{ route(RouteNames::PROFILE_COUNTERS) }}">
                    <div class="card-body">
                        {{ RouteNames::name(RouteNames::PROFILE_COUNTERS) }}
                    </div>
                </a>
                <a class="card mt-2 page-card text-decoration-none profile-quick-link"
                   href="{{ route(RouteNames::PROFILE_INVOICES) }}">
                    <div class="card-body">
                        {{ RouteNames::name(RouteNames::PROFILE_INVOICES) }}
                    </div>
                </a>
            @endif
        </div>
    </div>
@endsection
