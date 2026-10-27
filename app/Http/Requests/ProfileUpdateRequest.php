<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
     * Normalize the WhatsApp number to the canonical 505XXXXXXXX format.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone_whatsapp' => $this->normalizePhone($this->input('phone_whatsapp')),
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
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone_whatsapp' => ['required', 'string', 'regex:/^505\d{8}$/'],
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
            'phone_whatsapp.regex' => 'El número de WhatsApp debe tener 8 dígitos, opcionalmente precedido por el código de país 505.',
        ];
    }

    /**
     * Strip every non-digit character and prepend the country code when omitted.
     */
    private function normalizePhone(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (strlen($digits) === 8) {
            return '505'.$digits;
        }

        return $digits;
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
