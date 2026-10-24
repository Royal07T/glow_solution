<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_seeker_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->text('bio')->nullable();
            $table->json('skills')->nullable();
            $table->string('education_level')->nullable();
            $table->string('experience_level')->nullable();
            $table->json('preferred_categories')->nullable();
            $table->json('preferred_locations')->nullable();
            $table->enum('employment_preference', ['full_time', 'part_time', 'contract', 'temporary', 'domestic', 'any'])->nullable();
            $table->boolean('available_for_work')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_seeker_profiles');
    }
};
