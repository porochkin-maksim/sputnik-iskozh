<?php declare(strict_types=1);

namespace Core\Domains\Infra\Tokens;

use App\Models\Infra\Token;

abstract class TokenFacade
{
    public static function save(array $data, ?string $id = null): string
    {
        $model = Token::updateOrCreate(
            [Token::ID => $id ? : self::uuid()],
            [Token::DATA => json_encode($data)],
        );

        return $model->id;
    }

    public static function find(string $token): null|string|array
    {
        try {
            $model = Token::find($token);

            return $model->data ? json_decode($model->data, true) : null;
        }
        catch (\Throwable $e) {
            return null;
        }
    }

    public static function drop(string $id): void
    {
        Token::where(Token::ID, $id)->delete();
    }

    private static function uuid(): string
    {
        $bytes    = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        );
    }
}
