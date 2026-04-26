<?php declare(strict_types=1);

namespace App\Services\Users;

use App\Models\User;
use Core\Domains\User\UserEntity;
use Illuminate\Support\Facades\Password;

class Notificator
{
    public function sendRestorePassword(?UserEntity $user): void
    {
        if ( ! $user?->getId()) {
            return;
        }

        $model = User::find($user?->getId());
        if ( ! $model) {
            return;
        }

        $token = Password::createToken($model);
        $model->sendPasswordResetNotification($token);
    }

    public function sendInviteNotification(?UserEntity $user): void
    {
        if ( ! $user?->getId()) {
            return;
        }

        $model = User::find($user?->getId());
        if ( ! $model) {
            return;
        }

        $model->sendInviteNotification($user?->getEmail());
    }
}
