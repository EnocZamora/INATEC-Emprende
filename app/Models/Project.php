<?php

declare(strict_types=1);

namespace App\Models;

use App\Http\Policies\ProjectPolicy;
use App\Observers\ProjectObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'title', 'slug', 'description', 'category', 'cover_photo', 'status'])]
#[UsePolicy(ProjectPolicy::class)]
#[ObservedBy(ProjectObserver::class)]
class Project extends Model
{
    use SoftDeletes;

    public const string STATUS_DRAFT = 'draft';

    public const string STATUS_PUBLISHED = 'published';

    public const string STATUS_SUSPENDED = 'suspended';

    /**
     * Student who owns the project.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Additional photos attached to the project.
     *
     * @return HasMany<ProjectGallery, $this>
     */
    public function gallery(): HasMany
    {
        return $this->hasMany(ProjectGallery::class, 'project_id');
    }

    /**
     * WhatsApp deep link resolved from the owner profile.
     *
     * Contact data is never duplicated on the project: it always reads
     * `user.profile.phone_whatsapp`. Eager load `user.profile` on listings
     * to avoid N+1 queries.
     */
    protected function whatsappUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $phone = $this->user?->profile?->phone_whatsapp;

            if (blank($phone)) {
                return null;
            }

            $number = preg_replace('/\D+/', '', (string) $phone);

            if ($number === null || $number === '') {
                return null;
            }

            $message = rawurlencode(sprintf(
                'Hola, vi el proyecto "%s" en INATEC Emprende y quiero más información.',
                $this->title,
            ));

            return sprintf('https://wa.me/%s?text=%s', $number, $message);
        });
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Restrict the query to projects visible in the public catalog.
     *
     * @param  Builder<Project>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', self::STATUS_PUBLISHED);
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
            'deleted_at' => 'datetime',
        ];
    }
}
