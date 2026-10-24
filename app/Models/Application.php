<?php

namespace App\Models;

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
}
