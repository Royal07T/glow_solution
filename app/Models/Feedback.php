<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    public const TYPES = ['general', 'job', 'application'];

    protected $fillable = [
        'user_id',
        'job_id',
        'application_id',
        'type',
        'rating',
        'subject',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'job' => 'Job',
            'application' => 'Application',
            default => 'General',
        };
    }
}
