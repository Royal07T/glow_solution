<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('target_role', ['job_seeker', 'employer']);
            $table->enum('interval', ['weekly', 'monthly', 'quarterly', 'annual']);
            $table->decimal('price', 15, 2)->default(0);
            $table->string('currency', 3)->default('NGN');
            $table->text('description')->nullable();
            $table->json('entitlements');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
