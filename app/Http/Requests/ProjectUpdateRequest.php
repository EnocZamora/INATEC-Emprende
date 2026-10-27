<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->project();

        return $project !== null
            && ($this->user()?->can('update', $project) ?? false);
    }

    /**
     * Sanitize the editable text fields before validation.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        foreach (['title', 'description', 'category'] as $field) {
            if ($this->has($field)) {
                $sanitized[$field] = $this->sanitize($this->input($field));
            }
        }

        if ($sanitized !== []) {
            $this->merge($sanitized);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $statuses = [Project::STATUS_DRAFT, Project::STATUS_PUBLISHED];

        if ($this->user()?->isAdmin() ?? false) {
            $statuses[] = Project::STATUS_SUSPENDED;
        }

        return [
            'title' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'required', 'string', 'max:10000'],
            'category' => [
                'sometimes',
                'required',
                'string',
                Rule::in(ProjectStoreRequest::CATEGORIES),
            ],
            'cover_photo' => ['sometimes', 'nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'status' => ['sometimes', 'required', 'string', Rule::in($statuses)],
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
     * Resolve the bound project, supporting both model binding and raw IDs.
     */
    private function project(): ?Project
    {
        $project = $this->route('project');

        return match (true) {
            $project instanceof Project => $project,
            is_numeric($project) => Project::withTrashed()->find((int) $project),
            default => null,
        };
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
}
