<?php declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\FeatureTestCase;

class LoginTest extends FeatureTestCase
{
    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticated();
    }

    public function test_login_with_invalid_password_returns_error(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(302);
        $this->assertGuest();
    }

    public function test_access_home_requires_authentication(): void
    {
        $response = $this->get('/home');

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    public function test_authenticated_user_can_access_home(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertOk();
    }

    public function test_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/logout');

        $response->assertStatus(302);
        $this->assertGuest();
    }
}
