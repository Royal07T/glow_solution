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

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::CLOSED => 'Closed',
            self::EXPIRED => 'Expired',
        };
    }

    public function variant(): string
    {
        return match($this) {
            self::DRAFT => 'secondary',
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::CLOSED => 'secondary',
            self::EXPIRED => 'danger',
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