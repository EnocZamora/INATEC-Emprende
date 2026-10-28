<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\UserProfile;
use Illuminate\Support\Facades\Storage;

class ProfileObserver
{
    /**
    * Remove the previous file whenever profile media is replaced.
     *
     * The new file is stored before the model is saved, so this hook can
     * safely delete the old path just after the database row is updated.
     */
    public function updated(UserProfile $profile): void
    {
        foreach (['avatar_path', 'banner_path', 'diploma_path'] as $attribute) {
            if (! $profile->wasChanged($attribute)) {
                continue;
            }

            $previous = $profile->getOriginal($attribute);
            $current = $profile->getAttribute($attribute);

            if (! is_string($previous) || $previous === '' || $previous === $current) {
                continue;
            }

            $this->deleteFile($previous);
        }
    }

    /**
    * Remove all profile media when the profile row is deleted.
     *
     * Elementary profile rows are also removed explicitly before deleting an
     * account, because a database cascade would bypass Eloquent events.
     */
    public function deleting(UserProfile $profile): void
    {
        $this->deleteFile($profile->avatar_path);
        $this->deleteFile($profile->banner_path);
        $this->deleteFile($profile->diploma_path);
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
