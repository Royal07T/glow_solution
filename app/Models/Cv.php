<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
