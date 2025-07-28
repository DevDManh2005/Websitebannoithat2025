<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'type',
        'quantity',
        'description',
        'user_id',
    ];

    /**
     * Relationships
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

     public function user()
    {
        return $this->belongsTo(User::class);
    }
}