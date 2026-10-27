<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_request_a_recovery_link(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('¿Olvidaste tu contraseña?')
            ->assertSee('Enviar enlace');
    }

    public function test_recovery_link_is_sent_for_existing_accounts(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'estudiante@inatec.edu.ni']);

        $this->post(route('password.email'), ['email' => 'estudiante@inatec.edu.ni'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', 'Solicitud de recuperación recibida.');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_unknown_addresses_receive_the_same_generic_response(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'nadie@inatec.edu.ni'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', 'Solicitud de recuperación recibida.')
            ->assertSessionHasNoErrors();
    }

    public function test_confirmation_state_replaces_the_form(): void
    {
        Notification::fake();

        User::factory()->create(['email' => 'estudiante@inatec.edu.ni']);

        $this->followingRedirects()
            ->post(route('password.email'), ['email' => 'estudiante@inatec.edu.ni'])
            ->assertOk()
            ->assertSee('Revisa tu correo')
            ->assertSee('estudiante@inatec.edu.ni')
            ->assertSee('Reenviar el enlace')
            ->assertDontSee('Enviar enlace');
    }

    public function test_recovery_requires_a_valid_email(): void
    {
        $this->post(route('password.email'), ['email' => 'no-es-un-correo'])
            ->assertSessionHasErrors('email');
    }

    public function test_reset_form_shows_the_target_account(): void
    {
        $this->get(route('password.reset', [
            'token' => 'demo-token',
            'email' => 'estudiante@inatec.edu.ni',
        ]))
            ->assertOk()
            ->assertSee('estudiante@inatec.edu.ni')
            ->assertSee('Crea tu contraseña');
    }

    public function test_reset_form_handles_links_without_an_email(): void
    {
        $this->get(route('password.reset', ['token' => 'demo-token']))
            ->assertOk()
            ->assertSee('Solicitar un enlace nuevo');
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveVieja123',
        ]);

        $token = Password::createToken($user);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveNueva123',
            'password_confirmation' => 'ClaveNueva123',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('ClaveNueva123', $user->fresh()->password));
    }

    public function test_reset_requires_a_matching_confirmation(): void
    {
        $user = User::factory()->create([
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveVieja123',
        ]);

        $token = Password::createToken($user);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveNueva123',
            'password_confirmation' => 'ClaveDistinta123',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('ClaveVieja123', $user->fresh()->password));
    }

    public function test_invalid_tokens_are_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveVieja123',
        ]);

        $this->post(route('password.store'), [
            'token' => 'token-invalido',
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveNueva123',
            'password_confirmation' => 'ClaveNueva123',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('ClaveVieja123', $user->fresh()->password));
    }
}
