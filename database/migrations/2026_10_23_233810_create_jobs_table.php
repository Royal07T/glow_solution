<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['domestic', 'professional']);
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'temporary', 'live_in', 'live_out']);
            $table->text('description');
            $table->text('responsibilities')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('benefits')->nullable();
            $table->string('location');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->decimal('salary_min', 15, 2)->nullable();
            $table->decimal('salary_max', 15, 2)->nullable();
            $table->string('salary_currency', 3)->default('NGN');
            $table->boolean('salary_negotiable')->default(false);
            $table->date('application_deadline')->nullable();
            $table->integer('positions_available')->default(1);
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'closed', 'expired'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'published_at']);
            $table->index(['type', 'location']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
