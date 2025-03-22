<?php

namespace Core\Domains\Infra\Tokens;

use App\Models\Infra\Token;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

abstract class TokenFacade
{
    public static function save(string|array $arg): string
    {
        $data = Crypt::encrypt($arg);

        $model = Token::make([
            Token::ID   => Str::uuid()->serialize(),
            Token::DATA => $data,
        ]);

        $model->save();

        return $model->id;
    }

    public static function find(string $token): null|string|array
    {
        try {
            $model = Token::find($token);

            return $model->data ? Crypt::decrypt($model->data) : null;
        }
        catch (\Throwable) {
            return null;
        }
    }

    public static function drop(mixed $get): void
    {
        Token::where(Token::ID, $get)->delete();
    }
}