<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','type','value','min_order_amount','usage_limit','used_count',
        'start_at','end_at','is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at'  => 'datetime',
        'end_at'    => 'datetime',
        'value'     => 'decimal:0',
        'min_order_amount' => 'decimal:0',
    ];

    public function scopeActive($q) { return $q->where('is_active', 1); }

    public function scopeInEffect($q, ?Carbon $at = null)
    {
        $at = $at ?: now();
        return $q->where(function($qq) use ($at) {
            $qq->whereNull('start_at')->orWhere('start_at', '<=', $at);
        })->where(function($qq) use ($at) {
            $qq->whereNull('end_at')->orWhere('end_at', '>=', $at);
        });
    }

    public function scopeAvailable($q)
    {
        return $q->where(function($qq){
            $qq->whereNull('usage_limit')->orWhereColumn('used_count','<','usage_limit');
        });
    }
}
