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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function test_student_can_upload_avatar_and_banner(): void
    {
        Storage::fake('public');

        $this->actingAs($this->student)
            ->put(route('profile.update'), $this->payload([
                'avatar' => $this->fakeImage('avatar.png'),
                'banner' => $this->fakeImage('banner.png'),
            ]))
            ->assertRedirect(route('profile.show'))
            ->assertSessionHasNoErrors();

        $this->profile->refresh();

        $this->assertNotNull($this->profile->avatar_path);
        $this->assertNotNull($this->profile->banner_path);
        $this->assertStringStartsWith('profiles/avatars/', $this->profile->avatar_path);
        $this->assertStringStartsWith('profiles/banners/', $this->profile->banner_path);

        Storage::disk('public')->assertExists($this->profile->avatar_path);
        Storage::disk('public')->assertExists($this->profile->banner_path);
    }

    public function test_replacing_profile_media_deletes_the_previous_files(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('profiles/avatars/previous.png', 'old avatar');
        Storage::disk('public')->put('profiles/banners/previous.png', 'old banner');

        $this->profile->update([
            'avatar_path' => 'profiles/avatars/previous.png',
            'banner_path' => 'profiles/banners/previous.png',
        ]);

        $this->actingAs($this->student)
            ->put(route('profile.update'), $this->payload([
                'avatar' => $this->fakeImage('new-avatar.png'),
                'banner' => $this->fakeImage('new-banner.png'),
            ]))
            ->assertSessionHasNoErrors();

        $this->profile->refresh();

        $this->assertNotSame('profiles/avatars/previous.png', $this->profile->avatar_path);
        $this->assertNotSame('profiles/banners/previous.png', $this->profile->banner_path);

        Storage::disk('public')->assertMissing('profiles/avatars/previous.png');
        Storage::disk('public')->assertMissing('profiles/banners/previous.png');
        Storage::disk('public')->assertExists($this->profile->avatar_path);
        Storage::disk('public')->assertExists($this->profile->banner_path);
    }

    public function test_student_can_remove_avatar_and_banner(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('profiles/avatars/current.png', 'avatar');
        Storage::disk('public')->put('profiles/banners/current.png', 'banner');

        $this->profile->update([
            'avatar_path' => 'profiles/avatars/current.png',
            'banner_path' => 'profiles/banners/current.png',
        ]);

        $this->actingAs($this->student)
            ->put(route('profile.update'), $this->payload([
                'remove_avatar' => '1',
                'remove_banner' => '1',
            ]))
            ->assertSessionHasNoErrors();

        $this->profile->refresh();

        $this->assertNull($this->profile->avatar_path);
        $this->assertNull($this->profile->banner_path);

        Storage::disk('public')->assertMissing('profiles/avatars/current.png');
        Storage::disk('public')->assertMissing('profiles/banners/current.png');
    }

    public function test_profile_media_must_be_a_supported_image_under_two_megabytes(): void
    {
        Storage::fake('public');

        $this->actingAs($this->student)
            ->put(route('profile.update'), $this->payload([
                'avatar' => UploadedFile::fake()->create('avatar.pdf', 80, 'application/pdf'),
            ]))
            ->assertSessionHasErrors('avatar');

        $this->actingAs($this->student)
            ->put(route('profile.update'), $this->payload([
                'banner' => $this->fakeImage('banner.png', 3 * 1024 * 1024),
            ]))
            ->assertSessionHasErrors('banner');

        $this->assertNull($this->profile->refresh()->avatar_path);
        $this->assertNull($this->profile->banner_path);
    }

    public function test_administrators_cannot_upload_profile_media(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->put(route('profile.update'), [
                'name' => $admin->name,
                'email' => $admin->email,
                'phone_whatsapp' => '8888 8888',
                'avatar' => $this->fakeImage('avatar.png'),
                'banner' => $this->fakeImage('banner.png'),
            ])
            ->assertSessionHasErrors(['avatar', 'banner']);

        $this->assertNull($admin->profile?->avatar_path);
        $this->assertNull($admin->profile?->banner_path);
    }

    public function test_deleting_an_account_removes_its_profile_media(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('profiles/avatars/delete-me.png', 'avatar');
        Storage::disk('public')->put('profiles/banners/delete-me.png', 'banner');

        $this->profile->update([
            'avatar_path' => 'profiles/avatars/delete-me.png',
            'banner_path' => 'profiles/banners/delete-me.png',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $this->student))
            ->assertRedirect();

        $this->assertDatabaseMissing('user_profiles', ['user_id' => $this->student->id]);

        Storage::disk('public')->assertMissing('profiles/avatars/delete-me.png');
        Storage::disk('public')->assertMissing('profiles/banners/delete-me.png');
    }

    public function test_student_profile_pages_render_the_media_controls_and_avatar(): void
    {
        $this->profile->update(['avatar_path' => 'profiles/avatars/ana.png']);

        $this->actingAs($this->student)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('id="apariencia"', false)
            ->assertSee('name="avatar"', false)
            ->assertSee('name="banner"', false)
            ->assertSee('name="remove_avatar"', false);

        $this->actingAs($this->student)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('/storage/profiles/avatars/ana.png', false);
    }

    public function test_admin_profile_page_hides_the_media_controls(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Imagen de perfil reservada')
            ->assertDontSee('name="avatar"', false)
            ->assertDontSee('name="banner"', false);
    }

    public function test_promoting_a_student_to_admin_removes_profile_media(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('profiles/avatars/promoted.png', 'avatar');
        Storage::disk('public')->put('profiles/banners/promoted.png', 'banner');

        $this->profile->update([
            'avatar_path' => 'profiles/avatars/promoted.png',
            'banner_path' => 'profiles/banners/promoted.png',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->put(route('admin.users.role.update', $this->student), [
                'role' => User::ROLE_ADMIN,
            ])
            ->assertRedirect();

        $this->assertTrue($this->student->refresh()->isAdmin());
        $this->assertNull($this->profile->refresh()->avatar_path);
        $this->assertNull($this->profile->banner_path);

        Storage::disk('public')->assertMissing('profiles/avatars/promoted.png');
        Storage::disk('public')->assertMissing('profiles/banners/promoted.png');
    }

    /**
     * A real 1x1 PNG with optional padding, so validation exercises the real
     * image sniffing without depending on the GD extension.
     */
    private function fakeImage(string $name = 'avatar.png', int $padding = 0): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'profile').'.png';

        file_put_contents($path, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
        ).str_repeat("\0", $padding));

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    /**
     * @param  array<string, string|\Illuminate\Http\UploadedFile>  $overrides
     * @return array<string, string|\Illuminate\Http\UploadedFile>
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
