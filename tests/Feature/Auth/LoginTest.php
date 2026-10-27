<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_see_the_login_form(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Inicia sesión')
            ->assertSee('Correo electrónico');
    }

    public function test_students_can_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveSegura123',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveSegura123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_email_is_normalized_before_authentication(): void
    {
        $user = User::factory()->create([
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveSegura123',
        ]);

        $this->post(route('login'), [
            'email' => '  Estudiante@INATEC.edu.ni ',
            'password' => 'ClaveSegura123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create([
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveSegura123',
        ]);

        $this->post(route('login'), [
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'incorrecta',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_requires_an_email_and_password(): void
    {
        $this->post(route('login'), [])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_repeated_failures_are_rate_limited(): void
    {
        User::factory()->create([
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveSegura123',
        ]);

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login'), [
                'email' => 'estudiante@inatec.edu.ni',
                'password' => 'incorrecta',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login'), [
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveSegura123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_validation_messages_are_rendered_in_spanish(): void
    {
        $this->from(route('login'))
            ->followingRedirects()
            ->post(route('login'), ['email' => 'no-es-correo', 'password' => ''])
            ->assertOk()
            ->assertSee('Escribe un correo electrónico válido.')
            ->assertSee('Escribe tu contraseña.');
    }

    public function test_failed_credentials_are_reported_next_to_the_field(): void
    {
        User::factory()->create([
            'email' => 'estudiante@inatec.edu.ni',
            'password' => 'ClaveSegura123',
        ]);

        $this->from(route('login'))
            ->followingRedirects()
            ->post(route('login'), [
                'email' => 'estudiante@inatec.edu.ni',
                'password' => 'incorrecta',
            ])
            ->assertOk()
            ->assertSee('El correo o la contraseña no coinciden con ninguna cuenta.');
    }

    public function test_authenticated_users_are_redirected_away_from_the_login_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_students_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
