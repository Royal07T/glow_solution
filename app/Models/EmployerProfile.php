<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'company_name',
        'contact_person',
        'address',
        'city',
        'state',
        'country',
        'business_registration_number',
        'description',
        'logo',
        'verification_status',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifications()
    {
        return $this->hasMany(EmployerVerification::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function isVerified()
    {
        return $this->verification_status === 'approved';
    }
}
