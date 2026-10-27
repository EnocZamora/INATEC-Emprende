<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['municipality_id', 'name', 'code', 'location'])]
class Center extends Model
{
    /**
     * Municipality where the center operates.
     *
     * @return BelongsTo<Municipality, $this>
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Careers offered by the center.
     *
     * @return HasMany<Career, $this>
     */
    public function careers(): HasMany
    {
        return $this->hasMany(Career::class);
    }

    /**
     * Student profiles attached to the center.
     *
     * @return HasMany<UserProfile, $this>
     */
    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }
}
