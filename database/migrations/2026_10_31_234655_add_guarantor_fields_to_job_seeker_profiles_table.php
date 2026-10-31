<?php

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
        Schema::table('job_seeker_profiles', function (Blueprint $table) {
            $table->string('guarantor_name')->nullable()->after('identity_verification_notes');
            $table->string('guarantor_phone', 30)->nullable()->after('guarantor_name');
            $table->text('guarantor_address')->nullable()->after('guarantor_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_seeker_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'guarantor_name',
                'guarantor_phone',
                'guarantor_address',
            ]);
        });
    }
};
