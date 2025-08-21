<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    // type gợi ý: 'in' | 'out' | 'adjust'
    public const TYPE_IN     = 'in';
    public const TYPE_OUT    = 'out';
    public const TYPE_ADJUST = 'adjust';

    protected $fillable = [
        'inventory_id',
        'type',
        'quantity',
        'description',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /** Quan hệ */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
