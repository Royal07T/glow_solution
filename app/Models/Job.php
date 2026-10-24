<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_profile_id',
        'job_category_id',
        'title',
        'slug',
        'type',
        'employment_type',
        'description',
        'responsibilities',
        'qualifications',
        'benefits',
        'location',
        'city',
        'state',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_negotiable',
        'application_deadline',
        'positions_available',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'published_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'salary_negotiable' => 'boolean',
            'application_deadline' => 'date',
            'reviewed_at' => 'datetime',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function employerProfile()
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_jobs')->withTimestamps();
    }

    public function isApproved()
    {
        return $this->status === 'approved' && $this->published_at && $this->published_at->isPast();
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
