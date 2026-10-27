<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfilePasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display the authenticated account and its institutional profile.
     */
    public function show(Request $request): View
    {
        $user = $this->account($request);

        return view('profile.show', [
            'user' => $user,
            'profile' => $user->profile,
            'stats' => $this->projectStats($user),
        ]);
    }

    /**
     * Show the form for editing personal data, contact and credentials.
     */
    public function edit(Request $request): View
    {
        $user = $this->account($request);

        return view('profile.edit', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    /**
     * Persist the editable account data and the institutional contact card.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        DB::transaction(function () use ($user, $data): void {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            // A profile row exists from registration; updateOrCreate keeps
            // administrative accounts (which may lack one) working too.
            $user->profile()->updateOrCreate([], [
                'phone_whatsapp' => $data['phone_whatsapp'],
                'bio' => $data['bio'] ?? null,
            ]);
        });

        return redirect()
            ->route('profile.show')
            ->with('status', 'profile-updated');
    }

    /**
     * Replace the account password after proving the current one.
     */
    public function updatePassword(ProfilePasswordUpdateRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->string('password')->toString(),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Load the authenticated account with the relations rendered by both views.
     */
    private function account(Request $request): User
    {
        return $request->user()->load([
            'profile.center.municipality.department',
            'profile.career',
        ]);
    }

    /**
     * One grouped query keeps the profile summary free of N+1 counts.
     *
     * @return array{total: int, published: int, draft: int, suspended: int}
     */
    private function projectStats(User $user): array
    {
        $counts = $user->projects()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'total' => (int) $counts->sum(),
            'published' => (int) $counts->get(Project::STATUS_PUBLISHED, 0),
            'draft' => (int) $counts->get(Project::STATUS_DRAFT, 0),
            'suspended' => (int) $counts->get(Project::STATUS_SUSPENDED, 0),
        ];
    }
}
