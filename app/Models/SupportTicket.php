<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class SupportTicket extends Model
{
    protected $fillable = ['user_id','subject','message','status'];

    public const STATUS_OPEN         = 'open';
    public const STATUS_IN_PROGRESS  = 'in_progress';
    public const STATUS_RESOLVED     = 'resolved';
    public const STATUS_CLOSED       = 'closed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportReply::class)->latest();
    }
}
