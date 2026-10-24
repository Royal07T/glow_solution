<?php

namespace App\Enums;

enum UserRole: string
{
    case JOB_SEEKER = 'job_seeker';
    case EMPLOYER = 'employer';
    case ADMIN = 'admin';
}
