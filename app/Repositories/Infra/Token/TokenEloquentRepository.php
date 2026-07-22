<?php declare(strict_types=1);

namespace App\Repositories\Infra\Token;

use App\Models\Infra\Token;
use Core\Contracts\StringServiceInterface;
use Core\Domains\Infra\Tokens\TokenRepositoryInterface;

readonly class TokenEloquentRepository implements TokenRepositoryInterface
{
    public function __construct(
        private StringServiceInterface $stringService,
    )
    {
    }

    public function save(array $data, ?string $id = null): string
    {
        $model = Token::updateOrCreate(
            [Token::ID => $id ? : $this->stringService->uuid()],
            [Token::DATA => json_encode($data)],
        );

        return $model->id;
    }

    public function find(string $token): null|array
    {
        try {
            $model = Token::find($token);

            return $model->data ? json_decode($model->data, true) : null;
        }
        catch (\Throwable) {
            return null;
        }
    }

    public function drop(string $id): void
    {
        Token::where(Token::ID, $id)->delete();
    }
}
