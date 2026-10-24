<?php

namespace App\Enums;

enum JobStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CLOSED = 'closed';
    case EXPIRED = 'expired';
}
