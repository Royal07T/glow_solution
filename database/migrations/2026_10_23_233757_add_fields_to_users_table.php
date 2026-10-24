<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['job_seeker', 'employer', 'admin'])->default('job_seeker')->after('phone');
            $table->boolean('is_suspended')->default(false)->after('role');
            $table->timestamp('suspended_at')->nullable()->after('is_suspended');
            $table->text('suspension_reason')->nullable()->after('suspended_at');
            $table->timestamp('last_login_at')->nullable()->after('suspension_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'is_suspended', 'suspended_at', 'suspension_reason', 'last_login_at']);
        });
    }
};
