<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case SHORTLISTED = 'shortlisted';
    case INTERVIEW = 'interview';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';
}
