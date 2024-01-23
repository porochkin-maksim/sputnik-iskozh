<?php declare(strict_types=1);

namespace Core\Requests;

enum RequestArgumentsEnum: string
{
    const EMAIL    = 'email';
    const LOGIN    = 'login';
    const PASSWORD = 'password';
    const REMEMBER = 'remember';
}
