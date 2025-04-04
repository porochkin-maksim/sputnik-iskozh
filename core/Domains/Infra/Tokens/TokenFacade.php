<?php declare(strict_types=1);

namespace Core\Domains\Infra\Tokens;

use App\Models\Infra\Token;
use Illuminate\Support\Str;

abstract class TokenFacade
{
    public static function save(array $data): string
    {
        $model = Token::make([
            Token::ID   => Str::uuid()->serialize(),
            Token::DATA => json_encode($data),
        ]);

        $model->save();

        return $model->id;
    }

    public static function find(string $token): null|string|array
    {
        try {
            $model = Token::find($token);

            return $model->data ? json_decode($model->data, true): null;
        }
        catch (\Throwable $e) {
            return null;
        }
    }

    public static function drop(string $id): void
    {
        Token::where(Token::ID, $id)->delete();
    }
}