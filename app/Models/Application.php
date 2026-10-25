<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'job_seeker_id',
        'cv_id',
        'cover_letter',
        'status',
        'employer_notes',
        'applied_at',
        'status_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
            'status_updated_at' => 'datetime',
            'status' => ApplicationStatus::class,
        ];
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function jobSeeker()
    {
        return $this->belongsTo(User::class, 'job_seeker_id');
    }

    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }

    public function statusVariant(): string
    {
        if ($this->status instanceof ApplicationStatus) {
            return $this->status->variant();
        }
        if (is_string($this->status)) {
            $status = ApplicationStatus::tryFrom($this->status);
            if ($status) {
                return $status->variant();
            }
        }
        return 'neutral';
    }

    public function statusLabel(): string
    {
        if ($this->status instanceof ApplicationStatus) {
            return $this->status->label();
        }
        if (is_string($this->status)) {
            $status = ApplicationStatus::tryFrom($this->status);
            if ($status) {
                return $status->label();
            }
        }
        return $this->status ? ucfirst(str_replace('_', ' ', $this->status)) : 'Unknown';
    }

    public function canBeWithdrawn(): bool
    {
        $status = $this->status instanceof ApplicationStatus ? $this->status : ApplicationStatus::tryFrom($this->status);
        return in_array($status, [ApplicationStatus::SUBMITTED, ApplicationStatus::UNDER_REVIEW]);
    }
}
