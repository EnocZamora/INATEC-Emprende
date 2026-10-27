<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStoreRequest;
use App\Models\Career;
use App\Models\Center;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display the public project catalog with combinable filters.
     */
    public function index(Request $request): View
    {
        $departmentId = $request->integer('department_id');
        $municipalityId = $request->integer('municipality_id');
        $centerId = $request->integer('center_id');
        $careerId = $request->integer('career_id');
        $category = $this->queryString($request, 'category');
        $search = $this->queryString($request, 'search');

        if (! in_array($category, ProjectStoreRequest::CATEGORIES, true)) {
            $category = '';
        }

        $projects = Project::query()
            ->published()
            ->with([
                'user.profile',
                'user.profile.career',
                'user.profile.center',
                'user.profile.center.municipality',
                'user.profile.center.municipality.department',
                'gallery',
            ])
            ->when($departmentId > 0, fn (Builder $query): Builder => $query->whereHas(
                'user.profile.center.municipality',
                fn (Builder $municipality): Builder => $municipality->where('department_id', $departmentId),
            ))
            ->when($municipalityId > 0, fn (Builder $query): Builder => $query->whereHas(
                'user.profile.center',
                fn (Builder $center): Builder => $center->where('municipality_id', $municipalityId),
            ))
            ->when($centerId > 0, fn (Builder $query): Builder => $query->whereHas(
                'user.profile',
                fn (Builder $profile): Builder => $profile->where('center_id', $centerId),
            ))
            ->when($careerId > 0, fn (Builder $query): Builder => $query->whereHas(
                'user.profile',
                fn (Builder $profile): Builder => $profile->where('career_id', $careerId),
            ))
            ->when($category !== '', fn (Builder $query): Builder => $query->where('category', $category))
            ->when($search !== '', fn (Builder $query): Builder => $query->where(
                fn (Builder $term): Builder => $term
                    ->whereLike('title', "%{$search}%")
                    ->orWhereLike('description', "%{$search}%"),
            ))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $view = match (true) {
            $request->routeIs('home') => 'pages.home',
            $request->routeIs('catalog.search') => 'catalog.search',
            default => 'catalog.index',
        };

        return view($view, [
            'projects' => $projects,
            'departments' => Department::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'municipalities' => $departmentId > 0
                ? Municipality::query()
                    ->where('department_id', $departmentId)
                    ->orderBy('name')
                    ->get(['id', 'name', 'department_id'])
                : collect(),
            'centers' => Center::query()
                ->with('municipality:id,name')
                ->orderBy('name')
                ->get(['id', 'name', 'municipality_id']),
            'careers' => $centerId > 0
                ? Career::query()
                    ->where('center_id', $centerId)
                    ->orderBy('name')
                    ->get(['id', 'name', 'center_id'])
                : collect(),
            'categories' => ProjectStoreRequest::CATEGORIES,
            'filters' => [
                'department_id' => $departmentId,
                'municipality_id' => $municipalityId,
                'center_id' => $centerId,
                'career_id' => $careerId,
                'category' => $category,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Display a single project showcase page.
     */
    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $project->loadMissing([
            'user.profile',
            'user.profile.center',
            'user.profile.center.municipality',
            'user.profile.center.municipality.department',
            'gallery',
        ]);

        $related = Project::query()
            ->published()
            ->where('category', $project->category)
            ->whereKeyNot($project->getKey())
            ->with([
                'user.profile',
                'user.profile.center',
                'gallery',
            ])
            ->latest()
            ->limit(3)
            ->get();

        return view('projects.show', [
            'project' => $project,
            'related' => $related,
        ]);
    }

    /**
     * Read a trimmed string query parameter without risking array input.
     */
    private function queryString(Request $request, string $key): string
    {
        $value = $request->query($key);

        return is_string($value) ? trim($value) : '';
    }
}
