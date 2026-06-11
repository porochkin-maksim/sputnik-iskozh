<?php declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Infra\Token;
use App\Models\User;
use Carbon\Carbon;
use Tests\Feature\FeatureTestCase;

class SetPasswordTest extends FeatureTestCase
{
    private const string TOKEN_ID = 'e651cc04-248a-4846-85bc-6de8a5a7bad8';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
    }

    public function test_get_with_valid_token_returns_ok(): void
    {
        $this->createToken();

        $response = $this->get('/password/set?token=' . self::TOKEN_ID);

        $response->assertOk();
        $response->assertSee('test@example.com');
    }

    public function test_get_without_token_redirects_to_index(): void
    {
        $response = $this->get('/password/set');

        $response->assertRedirect('/');
    }

    public function test_get_with_invalid_token_redirects_to_index(): void
    {
        $response = $this->get('/password/set?token=nonexistent-token');

        $response->assertRedirect('/');
    }

    public function test_get_with_expired_token_redirects_to_index(): void
    {
        $this->createToken(Carbon::now()->subDay());

        $response = $this->get('/password/set?token=' . self::TOKEN_ID);

        $response->assertRedirect('/');
    }

    public function test_post_with_valid_data_sets_password_and_returns_json(): void
    {
        $this->createToken();

        $response = $this->postJson('/password/set', [
            'token'                 => self::TOKEN_ID,
            'email'                 => 'test@example.com',
            'password'              => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ]);

        $response->assertOk();
        $response->assertJson(['redirect' => route('home')]);
    }

    public function test_post_logs_user_in(): void
    {
        $this->createToken();

        $response = $this->postJson('/password/set', [
            'token'                 => self::TOKEN_ID,
            'email'                 => 'test@example.com',
            'password'              => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ]);

        $response->assertOk();
        $this->assertAuthenticated();
    }

    public function test_post_with_mismatched_passwords_returns_422(): void
    {
        $this->createToken();

        $response = $this->postJson('/password/set', [
            'token'                 => self::TOKEN_ID,
            'email'                 => 'test@example.com',
            'password'              => 'NewPass123',
            'password_confirmation' => 'Different1',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['password']]);
    }

    public function test_post_with_weak_password_returns_422(): void
    {
        $this->createToken();

        $response = $this->postJson('/password/set', [
            'token'                 => self::TOKEN_ID,
            'email'                 => 'test@example.com',
            'password'              => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['password']]);
    }

    public function test_post_with_expired_token_returns_422(): void
    {
        $this->createToken(Carbon::now()->subDay());

        $response = $this->postJson('/password/set', [
            'token'                 => self::TOKEN_ID,
            'email'                 => 'test@example.com',
            'password'              => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ]);

        $response->assertStatus(422);
    }

    public function test_post_with_invalid_token_returns_422(): void
    {
        $response = $this->postJson('/password/set', [
            'token'                 => 'nonexistent-token',
            'email'                 => 'test@example.com',
            'password'              => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ]);

        $response->assertStatus(422);
    }

    public function test_token_is_deleted_after_use(): void
    {
        $this->createToken();

        $response = $this->postJson('/password/set', [
            'token'                 => self::TOKEN_ID,
            'email'                 => 'test@example.com',
            'password'              => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ]);

        $response->assertOk();
        $this->assertNull(Token::find(self::TOKEN_ID));
    }

    private function createToken(?Carbon $expires = null): void
    {
        $expires ??= Carbon::now()->addHour();

        Token::create([
            'id'   => self::TOKEN_ID,
            'data' => json_encode([
                'email'   => 'test@example.com',
                'expires' => $expires->format('Y-m-d H:i:s'),
            ]),
        ]);
    }
}