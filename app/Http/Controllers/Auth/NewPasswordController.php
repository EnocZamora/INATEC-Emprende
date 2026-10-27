<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset form for the given token.
     */
    public function create(Request $request, string $token): View
    {
        $email = $request->query('email');

        return view('auth.reset-password', [
            'token' => $token,
            'email' => is_string($email) ? Str::lower(trim($email)) : '',
        ]);
    }

    /**
     * Validate the token and persist the new password.
     */
    public function store(NewPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request): void {
                $user->forceFill([
                    'password' => $request->string('password')->toString(),
                    // Regenerating the remember token kills forgotten-device cookies.
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('status', 'Tu contraseña fue actualizada. Inicia sesión con la nueva.');
        }

        return redirect()
            ->route('password.reset', [
                'token' => $request->string('token')->toString(),
                'email' => $request->string('email')->toString(),
            ])
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Solicita uno nuevo para continuar.',
            ]);
    }
}
