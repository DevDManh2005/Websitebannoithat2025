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
}