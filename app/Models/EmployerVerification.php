<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerVerification extends Model
{
    use HasFactory;

    public const DOCUMENT_TYPES = [
        'cac_certificate' => 'CAC Certificate',
        'business_registration' => 'Business Registration Certificate',
        'government_id' => 'Government-issued ID',
    ];

    protected $fillable = [
        'employer_profile_id',
        'document_type',
        'document_path',
        'notes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function employerProfile()
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function documentTypeLabel(): string
    {
        return self::DOCUMENT_TYPES[$this->document_type]
            ?? ucfirst(str_replace('_', ' ', $this->document_type ?: 'Document'));
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
