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
use Tests\TestCase;

class PublicPortalTest extends TestCase
{
    use RefreshDatabase;

    private Department $managua;

    private Department $leon;

    private Center $center;

    private Career $career;

    private User $student;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->managua = Department::query()->create(['name' => 'Managua', 'code' => 'MN']);
        $this->leon = Department::query()->create(['name' => 'León', 'code' => 'LE']);

        $municipality = Municipality::query()->create([
            'department_id' => $this->managua->id,
            'name' => 'Managua',
        ]);

        $this->center = Center::query()->create([
            'municipality_id' => $municipality->id,
            'name' => 'Centro Tecnológico de Managua',
            'code' => 'CT-MN-001',
            'location' => 'Managua',
        ]);

        $this->career = Career::query()->create([
            'center_id' => $this->center->id,
            'name' => 'Técnico en Desarrollo de Software',
        ]);

        $this->student = User::factory()->create([
            'name' => 'Estudiante INATEC',
            'role' => User::ROLE_STUDENT,
        ]);

        UserProfile::query()->create([
            'user_id' => $this->student->id,
            'center_id' => $this->center->id,
            'career_id' => $this->career->id,
            'phone_whatsapp' => '50588888888',
            'bio' => 'Estudiante de desarrollo de software.',
        ]);

        $this->project = Project::query()->create([
            'user_id' => $this->student->id,
            'title' => 'Sistema de riego automatizado',
            'slug' => 'sistema-de-riego-automatizado',
            'description' => 'Mide la humedad del suelo y activa el riego solo cuando el cultivo lo necesita.',
            'category' => 'Agropecuaria',
            'cover_photo' => 'projects/riego.jpg',
            'status' => Project::STATUS_PUBLISHED,
        ]);
    }

    public function test_public_pages_render_without_errors(): void
    {
        $this->get('/')->assertOk()->assertSee('Proyectos estudiantiles');
        $this->get('/catalog')->assertOk()->assertSee('Catálogo de proyectos');
        $this->get('/search')->assertOk()->assertSee('Encuentra un proyecto');
        $this->get('/terminos')->assertOk()->assertSee('Términos de uso');
        $this->get('/privacidad')->assertOk()->assertSee('Política de privacidad');
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_catalog_only_lists_published_projects(): void
    {
        Project::query()->create([
            'user_id' => $this->student->id,
            'title' => 'Borrador secreto',
            'slug' => 'borrador-secreto',
            'description' => 'Todavía no está listo para el público.',
            'category' => 'Tecnología',
            'cover_photo' => 'projects/borrador.jpg',
            'status' => Project::STATUS_DRAFT,
        ]);

        $this->get('/catalog')
            ->assertOk()
            ->assertSee('Sistema de riego automatizado')
            ->assertDontSee('Borrador secreto');
    }

    public function test_catalog_filters_by_category(): void
    {
        $this->get('/catalog?category=Agropecuaria')
            ->assertOk()
            ->assertSee('Sistema de riego automatizado');

        $this->get('/catalog?category=Tecnología')
            ->assertOk()
            ->assertDontSee('Sistema de riego automatizado');
    }

    public function test_catalog_ignores_unknown_category(): void
    {
        $this->get('/catalog?category=NoExiste')
            ->assertOk()
            ->assertSee('Sistema de riego automatizado');
    }

    public function test_catalog_filters_by_department_center_and_career(): void
    {
        $this->get('/catalog?department_id='.$this->managua->id)
            ->assertOk()
            ->assertSee('Sistema de riego automatizado');

        $this->get('/catalog?department_id='.$this->leon->id)
            ->assertOk()
            ->assertDontSee('Sistema de riego automatizado');

        $this->get('/catalog?center_id='.$this->center->id)
            ->assertOk()
            ->assertSee('Sistema de riego automatizado');

        $this->get('/catalog?career_id='.$this->career->id)
            ->assertOk()
            ->assertSee('Sistema de riego automatizado');
    }

    public function test_search_matches_title_and_description(): void
    {
        $this->get('/search?search=riego')
            ->assertOk()
            ->assertSee('Resultados para')
            ->assertSee('Sistema de riego automatizado');

        $this->get('/search?search=humedad')
            ->assertOk()
            ->assertSee('Sistema de riego automatizado');

        $this->get('/search?search=inexistente')
            ->assertOk()
            ->assertSee('Sin resultados');
    }

    public function test_project_page_is_public_when_published(): void
    {
        $this->get('/projects/sistema-de-riego-automatizado')
            ->assertOk()
            ->assertSee('Sistema de riego automatizado')
            ->assertSee('Estudiante INATEC')
            ->assertSee('wa.me/50588888888', false)
            ->assertSee('Escribir por WhatsApp');
    }

    public function test_draft_project_is_hidden_from_guests(): void
    {
        $this->project->update(['status' => Project::STATUS_DRAFT]);

        $this->get('/projects/sistema-de-riego-automatizado')->assertForbidden();
    }

    public function test_draft_project_is_visible_to_its_owner_with_preview_notice(): void
    {
        $this->project->update(['status' => Project::STATUS_DRAFT]);

        $this->actingAs($this->student)
            ->get('/projects/sistema-de-riego-automatizado')
            ->assertOk()
            ->assertSee('Vista previa de borrador');
    }

    public function test_suspended_project_is_hidden_from_guests_and_visible_to_admin(): void
    {
        $this->project->update(['status' => Project::STATUS_SUSPENDED]);

        $this->get('/projects/sistema-de-riego-automatizado')->assertForbidden();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get('/projects/sistema-de-riego-automatizado')
            ->assertOk()
            ->assertSee('Proyecto suspendido');
    }

    public function test_home_shows_catalog_totals(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('El catálogo hoy')
            ->assertSee('Centro Tecnológico de Managua');
    }
}
