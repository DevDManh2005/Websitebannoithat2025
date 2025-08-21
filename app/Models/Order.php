<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;

    // Trạng thái chuẩn
    public const ST_PENDING   = 'pending';
    public const ST_PROCESS   = 'processing';
    public const ST_SHIP_OUT  = 'shipped_to_shipper';
    public const ST_SHIPPING  = 'shipping';
    public const ST_DELIVERED = 'delivered';
    public const ST_RECEIVED  = 'received';
    public const ST_CANCEL    = 'cancelled';

    protected $fillable = [
        'user_id',
        'order_code',
        'total_amount',
        'discount',
        'final_amount',
        'status',
        'note',
        'payment_method',
        'is_paid',
        'payment_status',
        'paid_at',
        'payment_ref',
    ];

    protected $casts = [
        'is_paid'       => 'boolean',
        'paid_at'       => 'datetime',
        // Nếu VND không có lẻ: decimal:0 (nếu có lẻ đổi thành :2)
        'total_amount'  => 'decimal:0',
        'discount'      => 'decimal:0',
        'final_amount'  => 'decimal:0',
    ];

    /* Quan hệ */
    public function user()     { return $this->belongsTo(User::class); }
    public function items()    { return $this->hasMany(OrderItem::class); }
    public function shipment() { return $this->hasOne(Shipment::class); }
    public function payment()  { return $this->hasOne(Payment::class); }

    /* Helper logic UI */
    public function isCancellable(): bool
    {
        return in_array($this->status, [self::ST_PENDING, self::ST_PROCESS], true);
    }

    public function isReceivableByCustomer(): bool
    {
        return $this->status === self::ST_DELIVERED;
    }

    public static function getStatusText($status)
    {
        return [
            self::ST_PENDING   => 'Chờ xử lý',
            self::ST_PROCESS   => 'Đang xử lý',
            self::ST_SHIP_OUT  => 'Đã giao shipper',
            self::ST_SHIPPING  => 'Đang giao',
            self::ST_DELIVERED => 'Đã giao',
            self::ST_RECEIVED  => 'Đã nhận',
            self::ST_CANCEL    => 'Đã hủy',
        ][$status] ?? 'Không xác định';
    }

    public static function getStatusClass($status)
    {
        return [
            self::ST_PENDING   => 'bg-warning text-dark',
            self::ST_PROCESS   => 'bg-info text-dark',
            self::ST_SHIP_OUT  => 'bg-secondary',
            self::ST_SHIPPING  => 'bg-primary',
            self::ST_DELIVERED => 'bg-success',
            self::ST_RECEIVED  => 'bg-success',
            self::ST_CANCEL    => 'bg-danger',
        ][$status] ?? 'bg-secondary';
    }

    /* ===== ACCESSOR & SCOPES CHO BÁO CÁO ===== */

    // Số tiền thực tế dùng cho doanh thu
    public function getAmountAttribute()
    {
        return $this->final_amount ?? $this->total_amount ?? 0;
    }

    // Đơn đã thanh toán (phủ cả 2 mô hình: is_paid hoặc payment_status='paid')
    public function scopePaid(Builder $q): Builder
    {
        return $q->where(function ($qq) {
            $qq->where('is_paid', 1)
               ->orWhere('payment_status', 'paid');
        });
    }

    public function scopeDateBetween(Builder $q, $from, $to): Builder
    {
        return $q->when($from, fn($qq)=>$qq->where('created_at', '>=', $from))
                 ->when($to,   fn($qq)=>$qq->where('created_at', '<=', $to));
    }

    public function scopeStatus(Builder $q, ?string $status): Builder
    {
        return $q->when($status, fn($qq)=>$qq->where('status', $status));
    }
}
