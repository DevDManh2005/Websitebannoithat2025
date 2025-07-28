<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'receiver_name',
        'phone',
        'address',
        'city',
        'district',
        'ward',
        'shipping_fee',
        'tracking_code',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
