<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
