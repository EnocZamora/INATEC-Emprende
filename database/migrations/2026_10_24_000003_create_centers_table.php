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
        Schema::create('centers', function (Blueprint $table): void {
            $table->id();

            // Territorial catalogs are protected: never cascade-delete centers.
            $table->foreignId('municipality_id')->constrained('municipalities')->restrictOnDelete();

            $table->string('name', 120);
            $table->string('code', 20)->unique();
            $table->string('location', 255)->nullable();
            $table->timestamps();

            $table->index(['municipality_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centers');
    }
};
