<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Career;
use App\Models\Center;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private UserProfile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $department = Department::query()->create(['name' => 'Managua', 'code' => 'MN']);

        $municipality = Municipality::query()->create([
            'department_id' => $department->id,
            'name' => 'Managua',
        ]);

        $center = Center::query()->create([
            'municipality_id' => $municipality->id,
            'name' => 'Centro Tecnológico de Pruebas',
            'code' => 'CT-T-001',
            'location' => 'Managua',
        ]);

        $career = Career::query()->create([
            'center_id' => $center->id,
            'name' => 'Técnico en Desarrollo de Software',
        ]);

        $this->student = User::factory()->create([
            'name' => 'Ana Lucía Ríos',
            'email' => 'ana@inatec.edu.ni',
            'role' => User::ROLE_STUDENT,
        ]);

        $this->profile = UserProfile::query()->create([
            'user_id' => $this->student->id,
            'center_id' => $center->id,
            'career_id' => $career->id,
            'phone_whatsapp' => '50588888888',
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('profile.show'))->assertRedirect(route('login'));
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
        $this->put(route('profile.update'), [])->assertRedirect(route('login'));
        $this->put(route('profile.password.update'), [])->assertRedirect(route('login'));
    }

    public function test_student_sees_own_profile_summary(): void
    {
        $this->actingAs($this->student)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Ana Lucía Ríos')
            ->assertSee('ana@inatec.edu.ni')
            ->assertSee('Centro Tecnológico de Pruebas')
            ->assertSee('Técnico en Desarrollo de Software')
            ->assertSee('+505 8888 8888')
            ->assertSee('Ficha institucional');
    }

    public function test_student_can_update_personal_data_and_contact(): void
    {
        $this->actingAs($this->student)
            ->put(route('profile.update'), [
                'name' => '  Ana Lucía   Ríos Gómez ',
                'email' => 'ANA.GOMEZ@INATEC.EDU.NI',
                'phone_whatsapp' => '+505 7777-7777',
                'bio' => '<b>Soy estudiante</b> de desarrollo de software.',
            ])
            ->assertRedirect(route('profile.show'))
            ->assertSessionHas('status', 'profile-updated');

        $this->student->refresh();
        $this->profile->refresh();

        $this->assertSame('Ana Lucía Ríos Gómez', $this->student->name);
        $this->assertSame('ana.gomez@inatec.edu.ni', $this->student->email);
        $this->assertSame('50577777777', $this->profile->phone_whatsapp);
        $this->assertSame('Soy estudiante de desarrollo de software.', $this->profile->bio);
    }

    public function test_profile_update_rejects_an_invalid_whatsapp_number(): void
    {
        foreach (['1234 5678', '8888 8888 8', 'llámame'] as $phone) {
            $this->actingAs($this->student)
                ->put(route('profile.update'), $this->payload(['phone_whatsapp' => $phone]))
                ->assertSessionHasErrors('phone_whatsapp');
        }

        $this->assertSame('50588888888', $this->profile->refresh()->phone_whatsapp);
    }

    public function test_profile_update_keeps_the_current_email_and_rejects_another_users_email(): void
    {
        $other = User::factory()->create(['email' => 'otro@inatec.edu.ni']);

        $this->actingAs($this->student)
            ->put(route('profile.update'), $this->payload(['email' => $other->email]))
            ->assertSessionHasErrors('email');

        $this->actingAs($this->student)
            ->put(route('profile.update'), $this->payload(['email' => 'ana@inatec.edu.ni']))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.show'));
    }

    public function test_edit_page_renders_the_numeric_phone_field_and_password_form(): void
    {
        $this->actingAs($this->student)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('maxlength="8"', false)
            ->assertSee('pattern="[0-9]{8}"', false)
            ->assertSee('value="88888888"', false)
            ->assertSee('name="current_password"', false);
    }

    public function test_password_update_requires_the_current_password(): void
    {
        $this->actingAs($this->student)
            ->from(route('profile.edit'))
            ->put(route('profile.password.update'), [
                'current_password' => 'incorrecta',
                'password' => 'NuevaClave123',
                'password_confirmation' => 'NuevaClave123',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $this->student->refresh()->password));
    }

    public function test_student_can_update_password(): void
    {
        $this->actingAs($this->student)
            ->from(route('profile.edit'))
            ->put(route('profile.password.update'), [
                'current_password' => 'password',
                'password' => 'NuevaClave123',
                'password_confirmation' => 'NuevaClave123',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('NuevaClave123', $this->student->refresh()->password));
    }

    public function test_password_update_rejects_weak_or_unconfirmed_passwords(): void
    {
        $this->actingAs($this->student)
            ->put(route('profile.password.update'), [
                'current_password' => 'password',
                'password' => 'corta',
                'password_confirmation' => 'otra',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $this->student->refresh()->password));
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ana Lucía Ríos',
            'email' => 'ana@inatec.edu.ni',
            'phone_whatsapp' => '8888 8888',
            'bio' => 'Estudiante de desarrollo de software.',
        ], $overrides);
    }
}
