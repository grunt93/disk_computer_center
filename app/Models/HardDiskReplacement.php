<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HardDiskReplacement extends Model
{
    protected $fillable = [
        'user_id',
        'classroom_id',
        'is_replaced',
        'issues_found'
    ];

    protected $casts = [
        'is_replaced' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
