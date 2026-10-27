<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetLinkRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password recovery form.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Mail a recovery link to the address, if an account exists.
     */
    public function store(PasswordResetLinkRequest $request): RedirectResponse
    {
        // The broker result is intentionally ignored: telling visitors whether
        // an address is registered would turn this form into an enumeration
        // oracle. The response is always the same generic confirmation.
        Password::sendResetLink($request->only('email'));

        return redirect()
            ->route('password.request')
            ->withInput($request->only('email'))
            ->with('status', 'Solicitud de recuperación recibida.');
    }
}
