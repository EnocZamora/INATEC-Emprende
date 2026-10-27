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
        Schema::create('user_profiles', function (Blueprint $table): void {
            $table->id();

            // One profile per user (1:1). Deleting the account removes the profile.
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // Institutional catalogs are protected: never cascade-delete student data.
            $table->foreignId('center_id')->constrained('centers')->restrictOnDelete();
            $table->foreignId('career_id')->constrained('careers')->restrictOnDelete();

            // Normalized WhatsApp contact source of truth: e.g. 50588888888.
            $table->string('phone_whatsapp', 20)->index();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
