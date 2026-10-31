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
            $table->string('photo_path')->nullable()->after('user_id');
            $table->string('identification_type', 50)->nullable()->after('gender');
            $table->text('identification_number')->nullable()->after('identification_type');
            $table->string('identification_document_path')->nullable()->after('identification_number');
            $table->string('identity_verification_status', 20)->default('unsubmitted')->after('identification_document_path');
            $table->timestamp('identity_submitted_at')->nullable()->after('identity_verification_status');
            $table->foreignId('identity_reviewed_by')->nullable()->after('identity_submitted_at')->constrained('users')->nullOnDelete();
            $table->timestamp('identity_reviewed_at')->nullable()->after('identity_reviewed_by');
            $table->text('identity_verification_notes')->nullable()->after('identity_reviewed_at');
            $table->index('identity_verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_seeker_profiles', function (Blueprint $table) {
            $table->dropForeign(['identity_reviewed_by']);
            $table->dropIndex(['identity_verification_status']);
            $table->dropColumn([
                'photo_path',
                'identification_type',
                'identification_number',
                'identification_document_path',
                'identity_verification_status',
                'identity_submitted_at',
                'identity_reviewed_by',
                'identity_reviewed_at',
                'identity_verification_notes',
            ]);
        });
    }
};
