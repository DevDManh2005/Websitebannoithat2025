<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'total_amount',
        'discount',
        'final_amount',
        'status',
        'note',
        'payment_method',
        'is_paid','payment_method','payment_status','paid_at','payment_ref','status',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Kiểm tra xem đơn hàng có thể hủy được hay không.
     * CHO PHÉP HỦY KHI: chờ xử lý, đang xử lý, đã giao cho shipper.
     */
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Kiểm tra xem khách hàng có thể đánh dấu đã nhận hàng hay không.
     * NÚT NHẬN HÀNG SẼ HIỆN KHI: đang giao.
     */
    public function isReceivableByCustomer(): bool
    {
        return $this->status === 'delivered';
    }
    public static function getStatusText($status)
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped_to_shipper' => 'Đã giao shipper',
            'shipping' => 'Đang giao',
            'delivered' => 'Đã giao',
            'received' => 'Đã nhận',
            'cancelled' => 'Đã hủy',
        ];
        return $statuses[$status] ?? 'Không xác định';
    }

    public static function getStatusClass($status)
    {
        $classes = [
            'pending' => 'bg-warning text-dark',
            'processing' => 'bg-info text-dark',
            'shipped_to_shipper' => 'bg-secondary',
            'shipping' => 'bg-primary',
            'delivered' => 'bg-success',
            'received' => 'bg-success',
            'cancelled' => 'bg-danger',
        ];
        return $classes[$status] ?? 'bg-secondary';
    }
}
