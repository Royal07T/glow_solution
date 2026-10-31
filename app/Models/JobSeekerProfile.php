<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSeekerProfile extends Model
{
    use HasFactory;

    public const IDENTIFICATION_TYPES = [
        'nin' => 'National Identification Number (NIN)',
        'voters_card' => "Voter's Card",
        'drivers_license' => "Driver's Licence",
        'passport' => 'International Passport',
        'national_id' => 'National ID Card',
        'other' => 'Other government-issued ID',
    ];

    protected $fillable = [
        'user_id',
        'photo_path',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'country',
        'bio',
        'skills',
        'education_level',
        'experience_level',
        'preferred_categories',
        'preferred_locations',
        'employment_preference',
        'available_for_work',
        'identification_type',
        'identification_number',
        'identification_document_path',
        'identity_verification_status',
        'identity_submitted_at',
        'identity_reviewed_by',
        'identity_reviewed_at',
        'identity_verification_notes',
        'guarantor_name',
        'guarantor_phone',
        'guarantor_address',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'skills' => 'array',
            'preferred_categories' => 'array',
            'preferred_locations' => 'array',
            'available_for_work' => 'boolean',
            'identification_number' => 'encrypted',
            'identity_submitted_at' => 'datetime',
            'identity_reviewed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function identityReviewer()
    {
        return $this->belongsTo(User::class, 'identity_reviewed_by');
    }

    public function hasIdentification(): bool
    {
        return filled($this->identification_type)
            && filled($this->identification_number)
            && filled($this->identification_document_path);
    }

    public function identificationTypeLabel(): ?string
    {
        return self::IDENTIFICATION_TYPES[$this->identification_type] ?? null;
    }

    public function hasGuarantor(): bool
    {
        return filled($this->guarantor_name)
            && filled($this->guarantor_phone)
            && filled($this->guarantor_address);
    }

    public function hasCompleteIdentitySubmission(): bool
    {
        return $this->hasIdentification() && $this->hasGuarantor();
    }

    public function isIdentityVerified(): bool
    {
        return $this->identity_verification_status === 'approved';
    }

    public function identityStatusLabel(): string
    {
        return match ($this->identity_verification_status) {
            'pending' => 'Under review',
            'approved' => 'Verified',
            'rejected' => 'Rejected',
            default => 'Not submitted',
        };
    }

    public function identityStatusBadgeVariant(): string
    {
        return match ($this->identity_verification_status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'neutral',
        };
    }
}
