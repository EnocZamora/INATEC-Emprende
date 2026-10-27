<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'center_id', 'career_id', 'phone_whatsapp', 'bio'])]
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
            $digits = preg_replace('/\D+/', '', (string) $this->phone_whatsapp);

            if ($digits === null || $digits === '') {
                return null;
            }

            if (strlen($digits) === 11 && str_starts_with($digits, '505')) {
                return sprintf('+505 %s %s', substr($digits, 3, 4), substr($digits, 7, 4));
            }

            return '+'.$digits;
        });
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
