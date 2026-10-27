<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use App\Models\UserProfile;
use App\Support\WhatsAppNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * The route carries no target: the profile being edited is always the
     * authenticated account. The `profile`/`user` lookup is kept for a future
     * administrative editor so ownership keeps being enforced server-side.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        $target = $this->route('profile') ?? $this->route('user');

        if ($target === null) {
            return true;
        }

        $targetUserId = match (true) {
            $target instanceof UserProfile => $target->user_id,
            $target instanceof User => $target->id,
            is_numeric($target) => (int) $target,
            default => null,
        };

        if ($targetUserId === null) {
            return false;
        }

        return $user->isAdmin() || $user->id === $targetUserId;
    }

    /**
     * Normalize the identity fields and the WhatsApp number before validation.
     */
    protected function prepareForValidation(): void
    {
        $name = $this->input('name');
        $email = $this->input('email');

        $this->merge([
            'name' => is_string($name) ? Str::squish($name) : '',
            'email' => is_string($email) ? Str::lower(trim($email)) : '',
            'phone_whatsapp' => WhatsAppNumber::normalize($this->input('phone_whatsapp')),
            'bio' => $this->sanitizeBio($this->input('bio')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:120',
                "regex:/^[\p{L}\p{M}\s.'\-]+$/u",
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()?->id),
            ],
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone_whatsapp' => [
                'required',
                'string',
                'max:20',
                'regex:'.WhatsAppNumber::STORED_PATTERN,
            ],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Escribe tu nombre completo.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede superar los 120 caracteres.',
            'name.regex' => 'El nombre solo puede incluir letras, espacios, puntos, apóstrofes y guiones.',

            'email.required' => 'Escribe tu correo electrónico.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'email.unique' => 'Ya existe otra cuenta con este correo electrónico.',

            'bio.max' => 'La descripción personal no puede superar los 1000 caracteres.',

            'phone_whatsapp.required' => 'Escribe tu número de WhatsApp.',
            'phone_whatsapp.regex' => 'Escribe un número de WhatsApp válido de 8 dígitos, por ejemplo 8888 8888.',
        ];
    }

    /**
     * Get human readable attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'bio' => 'descripción personal',
            'phone_whatsapp' => 'WhatsApp',
        ];
    }

    /**
     * Strip markup and control characters from the biography.
     */
    private function sanitizeBio(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $clean = strip_tags($value);
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $clean) ?? '';
        $clean = trim($clean);

        return $clean === '' ? null : $clean;
    }
}
