<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case EMPLOYER = 'employer';
    case JOB_SEEKER = 'job_seeker';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Admin',
            self::EMPLOYER => 'Employer',
            self::JOB_SEEKER => 'Job Seeker',
        };
    }

    public function variant(): string
    {
        return match($this) {
            self::ADMIN => 'danger',
            self::EMPLOYER => 'primary',
            self::JOB_SEEKER => 'success',
        };
    }

    public function statusLabel(): string
    {
        return $this->label();
    }

    public function statusVariant(): string
    {
        return $this->variant();
    }
}