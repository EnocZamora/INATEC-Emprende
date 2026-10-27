<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Validation (Spanish)
|--------------------------------------------------------------------------
|
| Only the rules used by this portal are translated. Laravel falls back to
| the English catalogue for anything missing, so the file stays small and
| easy to audit.
|
*/

return [
    'accepted' => 'Debes aceptar :attribute.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña actual no es correcta.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'exists' => 'El valor seleccionado en :attribute no es válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El valor seleccionado en :attribute no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'array' => 'El campo :attribute no puede tener más de :max elementos.',
        'file' => 'El archivo :attribute no puede pesar más de :max kilobytes.',
        'numeric' => 'El campo :attribute no puede ser mayor que :max.',
        'string' => 'El campo :attribute no puede superar los :max caracteres.',
    ],
    'mimes' => 'El archivo :attribute debe ser de tipo: :values.',
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El archivo :attribute debe pesar al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'regex' => 'El formato del campo :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'unique' => 'El valor del campo :attribute ya está en uso.',

    'password' => [
        'letters' => 'La contraseña debe incluir al menos una letra.',
        'mixed' => 'La contraseña debe incluir al menos una letra mayúscula y una minúscula.',
        'numbers' => 'La contraseña debe incluir al menos un número.',
        'symbols' => 'La contraseña debe incluir al menos un símbolo.',
        'uncompromised' => 'La contraseña aparece en una filtración de datos. Elige otra.',
    ],

    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'phone_whatsapp' => 'número de WhatsApp',
        'career_id' => 'carrera',
        'center_id' => 'centro',
        'terms' => 'términos de uso',
        'title' => 'título',
        'description' => 'descripción',
        'category' => 'especialidad',
        'cover_photo' => 'portada',
        'status' => 'estado',
    ],
];
