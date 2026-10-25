<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'target_role',
        'interval',
        'price',
        'currency',
        'description',
        'entitlements',
        'is_active',
        'is_free',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'entitlements' => 'array',
            'is_active' => 'boolean',
            'is_free' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function endsAt(?Carbon $start = null): Carbon
    {
        $start ??= now();

        return match ($this->interval) {
            'weekly' => $start->copy()->addWeek(),
            'quarterly' => $start->copy()->addMonths(3),
            'annual' => $start->copy()->addYear(),
            default => $start->copy()->addMonth(),
        };
    }
}
