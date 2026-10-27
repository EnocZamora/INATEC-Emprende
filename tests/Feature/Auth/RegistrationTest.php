<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Career;
use App\Models\Center;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private Center $center;

    private Career $career;

    protected function setUp(): void
    {
        parent::setUp();

        $department = Department::query()->create([
            'name' => 'Managua',
            'code' => 'NI-MN',
        ]);

        $municipality = Municipality::query()->create([
            'department_id' => $department->id,
            'name' => 'Managua',
        ]);

        $this->center = Center::query()->create([
            'municipality_id' => $municipality->id,
            'name' => 'Centro Tecnológico de Pruebas',
            'code' => 'CT-T-001',
            'location' => 'Managua',
        ]);

        $this->career = Career::query()->create([
            'center_id' => $this->center->id,
            'name' => 'Técnico en Desarrollo de Software',
        ]);
    }

    /**
     * @return array<string, string|int>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => '  María   Fernanda López ',
            'email' => '  Maria.Lopez@Correo.com ',
            'phone_whatsapp' => '8888 8888',
            'career_id' => $this->career->id,
            'password' => 'ClaveSegura123',
            'password_confirmation' => 'ClaveSegura123',
            'terms' => '1',
            ...$overrides,
        ];
    }

    public function test_guests_can_see_the_registration_form_with_grouped_careers(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Crea tu cuenta')
            ->assertSee('Centro Tecnológico de Pruebas')
            ->assertSee('Técnico en Desarrollo de Software');
    }

    public function test_students_can_register_with_an_institutional_profile(): void
    {
        $response = $this->post(route('register'), $this->payload());

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $user = User::query()->where('email', 'maria.lopez@correo.com')->firstOrFail();

        $this->assertSame('María Fernanda López', $user->name);
        $this->assertSame(User::ROLE_STUDENT, $user->role);
        $this->assertTrue(Hash::check('ClaveSegura123', $user->password));

        $profile = $user->profile()->firstOrFail();

        $this->assertSame($this->center->id, $profile->center_id);
        $this->assertSame($this->career->id, $profile->career_id);
        $this->assertSame('50588888888', $profile->phone_whatsapp);

        $this->assertGuest();
    }

    public function test_center_is_derived_from_the_selected_career(): void
    {
        $otherCenter = Center::query()->create([
            'municipality_id' => $this->center->municipality_id,
            'name' => 'Centro Tecnológico Ajeno',
            'code' => 'CT-T-002',
            'location' => null,
        ]);

        $this->post(route('register'), $this->payload([
            'center_id' => $otherCenter->id,
        ]))->assertRedirect(route('login'));

        $profile = User::query()->where('email', 'maria.lopez@correo.com')->firstOrFail()->profile;

        $this->assertSame($this->center->id, $profile->center_id);
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'maria.lopez@correo.com']);

        $this->post(route('register'), $this->payload())
            ->assertSessionHasErrors('email');

        $this->assertSame(1, User::query()->count());
    }

    public function test_password_must_be_confirmed(): void
    {
        $this->post(route('register'), $this->payload([
            'password_confirmation' => 'OtraClave123',
        ]))->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_password_requires_letters_and_numbers(): void
    {
        $this->post(route('register'), $this->payload([
            'password' => 'sololetras',
            'password_confirmation' => 'sololetras',
        ]))->assertSessionHasErrors('password');
    }

    public function test_whatsapp_number_must_be_a_valid_nicaraguan_mobile(): void
    {
        $this->post(route('register'), $this->payload([
            'phone_whatsapp' => '1234 5678',
        ]))->assertSessionHasErrors('phone_whatsapp');
    }

    public function test_whatsapp_accepts_eight_national_digits_and_stores_the_country_code(): void
    {
        $this->post(route('register'), $this->payload([
            'phone_whatsapp' => '+505 7777-7777',
        ]))->assertRedirect(route('login'));

        $this->assertSame(
            '50577777777',
            User::query()->where('email', 'maria.lopez@correo.com')->firstOrFail()->profile->phone_whatsapp,
        );
    }

    public function test_whatsapp_rejects_more_than_eight_national_digits(): void
    {
        $this->post(route('register'), $this->payload([
            'phone_whatsapp' => '8888 8888 8',
        ]))->assertSessionHasErrors('phone_whatsapp');
    }

    public function test_whatsapp_rejects_text_input(): void
    {
        $this->post(route('register'), $this->payload([
            'phone_whatsapp' => 'llámame',
        ]))->assertSessionHasErrors('phone_whatsapp');
    }

    public function test_whatsapp_rejects_numbers_that_are_not_mobile_lines(): void
    {
        $this->post(route('register'), $this->payload([
            'phone_whatsapp' => '2222 2222',
        ]))->assertSessionHasErrors('phone_whatsapp');
    }

    public function test_the_whatsapp_field_accepts_eight_numeric_digits_only(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('maxlength="8"', false)
            ->assertSee('pattern="[0-9]{8}"', false)
            ->assertSee('autocomplete="tel-national"', false);
    }

    public function test_terms_must_be_accepted(): void
    {
        $this->post(route('register'), $this->payload(['terms' => '0']))
            ->assertSessionHasErrors('terms');
    }

    public function test_career_must_exist(): void
    {
        $this->post(route('register'), $this->payload([
            'career_id' => 999999,
        ]))->assertSessionHasErrors('career_id');
    }
}
