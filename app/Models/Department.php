<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['name', 'code'])]
class Department extends Model
{
    /**
     * Municipalities belonging to the department.
     *
     * @return HasMany<Municipality, $this>
     */
    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipality::class);
    }

    /**
     * INATEC centers located in the department.
     *
     * @return HasManyThrough<Center, Municipality, $this>
     */
    public function centers(): HasManyThrough
    {
        return $this->hasManyThrough(Center::class, Municipality::class);
    }
}
