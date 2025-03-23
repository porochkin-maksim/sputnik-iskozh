<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::CONTENT)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="">
                    <h3 class="alert text-center m-0">{{ __('Verify Your Email Address') }}</h3>

                    <div class="card-body text-center">
                        @if (session('resent'))
                            <div class="alert alert-success"
                                 role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        {{ __('Before proceeding, please check your email for a verification link.') }}
                        <br>
                        {{ __('If you did not receive the email') }},
                        <form class="d-inline"
                              method="POST"
                              action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit"
                                    class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>
                            .
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
