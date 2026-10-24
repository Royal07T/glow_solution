<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSeekerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'skills' => 'array',
            'preferred_categories' => 'array',
            'preferred_locations' => 'array',
            'available_for_work' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
