<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_seeker_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('cv_id')->nullable();
            $table->text('cover_letter')->nullable();
            $table->enum('status', ['submitted', 'under_review', 'shortlisted', 'interview', 'accepted', 'rejected', 'withdrawn'])->default('submitted');
            $table->text('employer_notes')->nullable();
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();
            $table->unique(['job_id', 'job_seeker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
