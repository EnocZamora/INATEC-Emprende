<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /**
     * Display the authenticated student's project dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $projects = $user
            ->projects()
            ->withCount('gallery')
            ->latest()
            ->paginate(10);

        // One grouped query keeps the dashboard summary accurate without N+1 counts.
        $statusCounts = $user->projects()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('projects.index', [
            'projects' => $projects,
            'stats' => [
                'total' => (int) $statusCounts->sum(),
                'published' => (int) $statusCounts->get(Project::STATUS_PUBLISHED, 0),
                'draft' => (int) $statusCounts->get(Project::STATUS_DRAFT, 0),
                'suspended' => (int) $statusCounts->get(Project::STATUS_SUSPENDED, 0),
            ],
            'profile' => $user->profile()
                ->with(['center.municipality.department', 'career'])
                ->first(),
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Project::class);

        return view('projects.create', [
            'categories' => ProjectStoreRequest::CATEGORIES,
            'profile' => $request->user()
                ?->profile()
                ->with(['center.municipality.department', 'career'])
                ->first(),
        ]);
    }

    /**
     * Store a newly created project owned by the authenticated user.
     */
    public function store(ProjectStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['cover_photo'] = $this->storeCoverPhoto($request);

        $project = $request->user()->projects()->create($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'project-created');
    }

    /**
     * Show the form for editing an existing project.
     */
    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        return view('projects.edit', [
            'project' => $project->load([
                'gallery',
                'user.profile.center.municipality.department',
                'user.profile.career',
            ]),
            'categories' => ProjectStoreRequest::CATEGORIES,
            'profile' => $project->user?->profile,
        ]);
    }

    /**
     * Update an existing project. Ownership is enforced by the policy and FormRequest.
     */
    public function update(ProjectUpdateRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $data = $request->validated();
        $cover = $request->file('cover_photo');

        if ($cover instanceof UploadedFile) {
            $data['cover_photo'] = $cover->store('projects', 'public');
        } else {
            unset($data['cover_photo']);
        }

        $project->update($data);

        return redirect()
            ->route('projects.edit', $project)
            ->with('status', 'project-updated');
    }

    /**
     * Soft delete a project. Files are preserved for restoration.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()
            ->route('dashboard')
            ->with('status', 'project-deleted');
    }

    /**
     * Persist the uploaded cover photo on the public disk.
     */
    private function storeCoverPhoto(ProjectStoreRequest $request): string
    {
        $cover = $request->file('cover_photo');

        if (! $cover instanceof UploadedFile) {
            throw ValidationException::withMessages([
                'cover_photo' => __('validation.required', ['attribute' => 'cover_photo']),
            ]);
        }

        return $cover->store('projects', 'public');
    }
}
