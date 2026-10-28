<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\ProfileObserver;
use App\Support\WhatsAppNumber;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['user_id', 'center_id', 'career_id', 'phone_whatsapp', 'bio', 'avatar_path', 'banner_path'])]
#[ObservedBy(ProfileObserver::class)]
class UserProfile extends Model
{
    /**
     * Account that owns the profile.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Technical center the student belongs to.
     *
     * @return BelongsTo<Center, $this>
     */
    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    /**
     * Career the student is enrolled in.
     *
     * @return BelongsTo<Career, $this>
     */
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    /**
     * Human-readable WhatsApp number for the public project page.
     *
     * Numbers are stored as normalized digits (`505XXXXXXXX`); grouping them
     * makes the contact detail scannable without changing the stored value.
     */
    protected function whatsappDisplay(): Attribute
    {
        return Attribute::get(function (): ?string {
            $national = WhatsAppNumber::national($this->phone_whatsapp);

            if ($national === '') {
                return null;
            }

            if (preg_match(WhatsAppNumber::NATIONAL_PATTERN, $national) === 1) {
                return sprintf('+505 %s %s', substr($national, 0, 4), substr($national, 4, 4));
            }

            return '+'.$national;
        });
    }

    /**
     * Public URL of the profile photo, resolved from the stored relative path.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->publicMediaUrl($this->avatar_path));
    }

    /**
     * Public URL of the profile cover, resolved from the stored relative path.
     */
    protected function bannerUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->publicMediaUrl($this->banner_path));
    }

    /**
     * Resolve a stored path against the public disk without exposing the disk.
     */
    private function publicMediaUrl(mixed $path): ?string
    {
        return is_string($path) && $path !== ''
            ? Storage::disk('public')->url($path)
            : null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'center_id' => 'integer',
            'career_id' => 'integer',
            'phone_whatsapp' => 'string',
        ];
    }
}
