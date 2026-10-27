<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Career;
use App\Models\Center;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\Project;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private User $otherStudent;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $department = Department::query()->create(['name' => 'Managua', 'code' => 'MN']);

        $municipality = Municipality::query()->create([
            'department_id' => $department->id,
            'name' => 'Managua',
        ]);

        $center = Center::query()->create([
            'municipality_id' => $municipality->id,
            'name' => 'Centro Tecnológico de Managua',
            'code' => 'CT-MN-001',
        ]);

        $career = Career::query()->create([
            'center_id' => $center->id,
            'name' => 'Técnico en Desarrollo de Software',
        ]);

        $this->student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $this->otherStudent = User::factory()->create(['role' => User::ROLE_STUDENT]);

        foreach ([$this->student, $this->otherStudent] as $user) {
            UserProfile::query()->create([
                'user_id' => $user->id,
                'center_id' => $center->id,
                'career_id' => $career->id,
                'phone_whatsapp' => '50588888888',
            ]);
        }

        $this->project = Project::query()->create([
            'user_id' => $this->student->id,
            'title' => 'Sistema de riego automatizado',
            'slug' => 'sistema-de-riego-automatizado',
            'description' => 'Mide la humedad del suelo y activa el riego cuando hace falta.',
            'category' => 'Agropecuaria',
            'cover_photo' => 'projects/riego.jpg',
            'status' => Project::STATUS_PUBLISHED,
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/dashboard/projects/create')->assertRedirect('/login');
        $this->get('/dashboard/projects/'.$this->project->id.'/edit')->assertRedirect('/login');
    }

    public function test_dashboard_lists_only_the_authenticated_users_projects(): void
    {
        Project::query()->create([
            'user_id' => $this->otherStudent->id,
            'title' => 'Proyecto ajeno',
            'slug' => 'proyecto-ajeno',
            'description' => 'No debería aparecer en este panel.',
            'category' => 'Tecnología',
            'cover_photo' => 'projects/ajeno.jpg',
            'status' => Project::STATUS_PUBLISHED,
        ]);

        $this->actingAs($this->student)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Sistema de riego automatizado')
            ->assertDontSee('Proyecto ajeno')
            ->assertSee('Técnico en Desarrollo de Software');
    }

    public function test_student_can_open_the_create_form(): void
    {
        $this->actingAs($this->student)
            ->get('/dashboard/projects/create')
            ->assertOk()
            ->assertSee('Presenta tu proyecto');
    }

    public function test_student_can_publish_a_project(): void
    {
        $response = $this->actingAs($this->student)->post('/dashboard/projects', [
            'title' => 'Panel solar para talleres',
            'category' => 'Medio Ambiente',
            'description' => 'Un panel solar didáctico que alimenta las prácticas del taller.',
            'status' => Project::STATUS_PUBLISHED,
            'cover_photo' => $this->fakeImage(),
        ]);

        $project = Project::query()->where('title', 'Panel solar para talleres')->firstOrFail();

        $response
            ->assertRedirect(route('projects.show', $project))
            ->assertSessionHas('status', 'project-created');

        $this->assertSame(Project::STATUS_PUBLISHED, $project->status);
        $this->assertTrue($project->user->is($this->student));
        Storage::disk('public')->assertExists($project->cover_photo);
    }

    public function test_project_creation_requires_a_cover_and_valid_fields(): void
    {
        $this->actingAs($this->student)
            ->post('/dashboard/projects', [
                'title' => '',
                'category' => 'NoExiste',
                'description' => '',
                'status' => 'borrador',
            ])
            ->assertSessionHasErrors(['title', 'category', 'description', 'status', 'cover_photo']);

        $this->assertDatabaseCount('projects', 1);
    }

    public function test_student_can_update_own_project_without_replacing_the_cover(): void
    {
        $this->actingAs($this->student)
            ->put('/dashboard/projects/'.$this->project->id, [
                'title' => 'Sistema de riego automatizado v2',
                'category' => 'Agropecuaria',
                'description' => 'Descripción actualizada del proyecto.',
                'status' => Project::STATUS_DRAFT,
            ])
            ->assertRedirect(route('projects.edit', $this->project))
            ->assertSessionHas('status', 'project-updated');

        $this->project->refresh();

        $this->assertSame('Sistema de riego automatizado v2', $this->project->title);
        $this->assertSame(Project::STATUS_DRAFT, $this->project->status);
        $this->assertSame('projects/riego.jpg', $this->project->cover_photo);
    }

    public function test_student_cannot_update_or_delete_another_users_project(): void
    {
        $this->actingAs($this->otherStudent)
            ->put('/dashboard/projects/'.$this->project->id, [
                'title' => 'Secuestrado',
                'category' => 'Tecnología',
                'description' => 'Intento de modificación ajena.',
                'status' => Project::STATUS_DRAFT,
            ])
            ->assertForbidden();

        $this->actingAs($this->otherStudent)
            ->delete('/dashboard/projects/'.$this->project->id)
            ->assertForbidden();

        $this->project->refresh();

        $this->assertSame('Sistema de riego automatizado', $this->project->title);
        $this->assertNull($this->project->deleted_at);
    }

    public function test_student_can_soft_delete_own_project(): void
    {
        $this->actingAs($this->student)
            ->delete('/dashboard/projects/'.$this->project->id)
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'project-deleted');

        $this->assertSoftDeleted($this->project);

        $this->get('/catalog')->assertOk()->assertDontSee('Sistema de riego automatizado');
    }

    public function test_owner_sees_preview_notice_for_draft_in_dashboard_link(): void
    {
        $this->project->update(['status' => Project::STATUS_DRAFT]);

        $this->actingAs($this->student)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Borrador');
    }

    private function fakeImage(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'cover').'.png';

        file_put_contents($path, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
        ));

        return new UploadedFile($path, 'cover.png', 'image/png', null, true);
    }
}
