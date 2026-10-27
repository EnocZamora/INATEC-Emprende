<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class ProjectObserver
{
    /**
     * Remove the previous cover photo when it is replaced.
     */
    public function updated(Project $project): void
    {
        if (! $project->wasChanged('cover_photo')) {
            return;
        }

        $previous = $project->getOriginal('cover_photo');

        if (! is_string($previous) || $previous === '' || $previous === $project->cover_photo) {
            return;
        }

        $this->deleteFile($previous);
    }

    /**
     * Remove every physical file before the project row is permanently deleted.
     *
     * Gallery rows still exist at this point, so their paths can be resolved
     * before the database cascade removes them.
     */
    public function forceDeleting(Project $project): void
    {
        $this->deleteFile($project->cover_photo);

        $project->gallery()
            ->pluck('image_path')
            ->each(fn (mixed $path) => $this->deleteFile($path));
    }

    /**
     * Delete a single file from the public disk when it is a valid path.
     */
    private function deleteFile(mixed $path): void
    {
        if (! is_string($path) || $path === '') {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
