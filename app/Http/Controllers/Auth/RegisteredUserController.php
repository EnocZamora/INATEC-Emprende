<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Career;
use App\Models\Center;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration form.
     */
    public function create(): View
    {
        // One eager load keeps the grouped career select free of N+1 queries.
        $centers = Center::query()
            ->with(['careers' => fn (HasMany $careers) => $careers->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('auth.register', [
            'centers' => $centers,
        ]);
    }

    /**
     * Create the student account and its institutional profile.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $career = Career::query()->findOrFail($request->integer('career_id'));

        DB::transaction(function () use ($request, $career): void {
            $user = User::query()->create([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => $request->string('password')->toString(),
                'role' => User::ROLE_STUDENT,
            ]);

            $user->profile()->create([
                'center_id' => $career->center_id,
                'career_id' => $career->id,
                'phone_whatsapp' => $request->string('phone_whatsapp')->toString(),
                'bio' => null,
            ]);
        });

        return redirect()
            ->route('login')
            ->with('status', 'Tu cuenta fue creada. Inicia sesión con tu correo y contraseña.');
    }
}
