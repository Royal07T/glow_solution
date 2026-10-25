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

    public function label(): string
    {
        return match($this) {
            self::SUBMITTED => 'Submitted',
            self::UNDER_REVIEW => 'Under review',
            self::SHORTLISTED => 'Shortlisted',
            self::INTERVIEW => 'Interview',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
            self::WITHDRAWN => 'Withdrawn',
        };
    }

    public function variant(): string
    {
        return match($this) {
            self::SUBMITTED => 'warning',
            self::UNDER_REVIEW => 'info',
            self::SHORTLISTED => 'primary',
            self::INTERVIEW => 'info',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
            self::WITHDRAWN => 'secondary',
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
