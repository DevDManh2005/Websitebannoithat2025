<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\InventoryService; // <— thêm dòng này

class PaymentObserver
{
    public function updated(Payment $payment): void
    {
        if ($payment->wasChanged('status') && $payment->status === 'paid') {
            if ($payment->order) {
                app(InventoryService::class)->deductForOrder($payment->order, null);
            }
        }
    }
}
