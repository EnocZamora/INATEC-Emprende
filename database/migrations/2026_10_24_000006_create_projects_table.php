<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('slug', 180)->unique();
            $table->text('description');
            $table->string('category', 50);
            $table->string('cover_photo', 255);

            // Contact data is intentionally NOT stored here: it lives in user_profiles.phone_whatsapp.
            $table->enum('status', ['draft', 'published', 'suspended'])->default('draft');

            $table->timestamps();
            $table->softDeletes();

            // Public catalog feed: published projects, newest first.
            $table->index(['status', 'created_at']);

            // Catalog filtering by category, restricted to visible projects.
            $table->index(['category', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
