<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\WhatsAppNumber;
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
