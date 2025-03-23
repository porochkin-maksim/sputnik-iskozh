<?php

namespace Core\Domains\User\Services;

use Core\Domains\User\Models\UserDTO;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class Notificator
{
    public function __construct()
    {
    }

    public function sendRestorePassword(?UserDTO $user): void
    {
        $model = $user?->getModel();
        if ( ! $user || ! $model) {
            return;
        }

        $token = Password::createToken($model);
        $model->sendPasswordResetNotification($token);
    }

    public function sendInviteNotification(?UserDTO $user): void
    {
        $model = $user?->getModel();
        if ( ! $user || ! $model) {
            return;
        }

        $model->sendInviteNotification($user->getEmail());
    }
}