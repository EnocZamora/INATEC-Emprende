<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRoleUpdateRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display the administrative user directory.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $search = $this->queryString($request, 'q');
        $role = $this->queryString($request, 'role');

        if (! in_array($role, [User::ROLE_STUDENT, User::ROLE_ADMIN], true)) {
            $role = '';
        }

        $users = User::query()
            ->with([
                'profile.center.municipality.department',
                'profile.career',
            ])
            ->when($search !== '', fn (Builder $query): Builder => $query->where(
                fn (Builder $term): Builder => $term
                    ->whereLike('name', "%{$search}%")
                    ->orWhereLike('email', "%{$search}%"),
            ))
            ->when($role !== '', fn (Builder $query): Builder => $query->where('role', $role))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'q' => $search,
                'role' => $role,
            ],
            'roles' => [User::ROLE_STUDENT, User::ROLE_ADMIN],
        ]);
    }

    /**
     * Update the role of a platform user. Self-promotion is blocked by policy.
     */
    public function updateRole(UserRoleUpdateRequest $request, User $user): RedirectResponse
    {
        $this->authorize('updateRole', $user);

        $user->update([
            'role' => $request->validated('role'),
        ]);

        // Administrators must not keep student media, so promotion clears
        // every path and ProfileObserver removes the files from the disk.
        if ($user->isAdmin()) {
            $user->profile()
                ->first()
            ?->update(['avatar_path' => null, 'banner_path' => null, 'diploma_path' => null]);
        }

        return back()->with('status', 'user-role-updated');
    }

    /**
     * Permanently delete a user account and purge owned project files.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        DB::transaction(function () use ($user): void {
            $user->projects()
                ->withTrashed()
                ->get()
                ->each(fn (Project $project) => $project->forceDelete());

            // Delete the profile through Eloquent so ProfileObserver can
            // remove its avatar and banner; a FK cascade would skip events.
            $user->profile()->first()?->delete();

            $user->delete();
        });

        return back()->with('status', 'user-deleted');
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
