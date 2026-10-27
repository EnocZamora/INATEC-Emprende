<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectStoreRequest extends FormRequest
{
    /**
     * Categories allowed for the institutional catalog.
     *
     * @var array<int, string>
     */
    public const array CATEGORIES = [
        'Tecnología',
        'Agropecuaria',
        'Industria',
        'Turismo',
        'Salud',
        'Servicios',
        'Comercio',
        'Artesanía',
        'Medio Ambiente',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Project::class) ?? false;
    }

    /**
     * Sanitize input and generate a collision-free slug before validation.
     */
    protected function prepareForValidation(): void
    {
        $title = $this->sanitize($this->input('title'));

        $this->merge([
            'title' => $title,
            'description' => $this->sanitize($this->input('description')),
            'category' => $this->sanitize($this->input('category')),
            'slug' => $this->uniqueSlug($title),
            'status' => $this->input('status', Project::STATUS_DRAFT),
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
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:180', Rule::unique(Project::class, 'slug')],
            'description' => ['required', 'string', 'max:10000'],
            'category' => ['required', 'string', Rule::in(self::CATEGORIES)],
            'cover_photo' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'status' => [
                'required',
                'string',
                Rule::in([Project::STATUS_DRAFT, Project::STATUS_PUBLISHED]),
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
            'title.required' => 'Escribe un título para tu proyecto.',
            'title.max' => 'El título no puede superar los 150 caracteres.',
            'description.required' => 'Describe tu proyecto.',
            'description.max' => 'La descripción no puede superar los 10.000 caracteres.',
            'category.required' => 'Selecciona una especialidad.',
            'category.in' => 'Selecciona una especialidad válida.',
            'cover_photo.required' => 'Sube una foto de portada.',
            'cover_photo.image' => 'La portada debe ser una imagen.',
            'cover_photo.max' => 'La portada no puede superar los 2 MB.',
            'cover_photo.mimes' => 'La portada debe ser un archivo JPG, PNG o WebP.',
            'status.required' => 'Selecciona el estado del proyecto.',
            'status.in' => 'Selecciona un estado válido.',
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
            'title' => 'título',
            'description' => 'descripción',
            'category' => 'especialidad',
            'cover_photo' => 'portada',
            'status' => 'estado',
        ];
    }

    /**
     * Strip markup and control characters to neutralize stored XSS.
     */
    private function sanitize(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        $clean = strip_tags($value);
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $clean) ?? '';

        return trim($clean);
    }

    /**
     * Build a unique slug, accounting for soft-deleted projects.
     */
    private function uniqueSlug(string $title): string
    {
        $base = Str::limit(Str::slug($title), 150, '') ?: 'proyecto';
        $slug = $base;
        $suffix = 2;

        while (Project::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
