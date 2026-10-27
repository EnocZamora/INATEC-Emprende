<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Support\WhatsAppNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize the identity fields before validation.
     *
     * The career select is grouped by center, so `center_id` is never trusted
     * from the client: it is derived from the chosen career in the controller.
     */
    protected function prepareForValidation(): void
    {
        $name = $this->input('name');
        $email = $this->input('email');

        $this->merge([
            'name' => is_string($name) ? Str::squish($name) : '',
            'email' => is_string($email) ? Str::lower(trim($email)) : '',
            'phone_whatsapp' => WhatsAppNumber::normalize($this->input('phone_whatsapp')),
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
                Rule::unique('users', 'email'),
            ],
            'phone_whatsapp' => [
                'required',
                'string',
                'max:20',
                'regex:'.WhatsAppNumber::STORED_PATTERN,
            ],
            'career_id' => [
                'required',
                'integer',
                Rule::exists('careers', 'id'),
            ],
            'password' => [
                'required',
                'string',
                'max:255',
                Password::min(8)->letters()->numbers(),
                'confirmed',
            ],
            'terms' => ['accepted'],
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
            'email.unique' => 'Ya existe una cuenta con este correo electrónico.',

            'phone_whatsapp.required' => 'Escribe tu número de WhatsApp.',
            'phone_whatsapp.regex' => 'Escribe un número de WhatsApp válido de 8 dígitos, por ejemplo 8888 8888.',

            'career_id.required' => 'Selecciona tu centro y carrera.',
            'career_id.exists' => 'La carrera seleccionada no está disponible.',

            'password.required' => 'Crea una contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede superar los 255 caracteres.',
            'password.letters' => 'La contraseña debe incluir al menos una letra.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            'terms.accepted' => 'Debes aceptar los términos de uso y la política de privacidad.',
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
            'phone_whatsapp' => 'WhatsApp',
            'career_id' => 'carrera',
            'password' => 'contraseña',
        ];
    }
}
