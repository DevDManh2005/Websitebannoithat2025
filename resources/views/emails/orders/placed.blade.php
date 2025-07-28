<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận Đơn hàng</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { text-align: center; margin-bottom: 20px; }
        .order-details, .shipping-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { text-align: center; margin-top: 20px; font-size: 0.9em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Cảm ơn bạn đã đặt hàng!</h2>
        </div>
        <p>Chào {{ $order->user->name }},</p>
        <p>Chúng tôi đã nhận được đơn hàng của bạn. Dưới đây là thông tin chi tiết:</p>

        <div class="order-details">
            <h3>Chi tiết Đơn hàng #{{ $order->order_code }}</h3>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->variant->product->name }}
                            <br>
                            <small>
                                @foreach($item->variant->attributes as $key => $value)
                                    {{ ucfirst($key) }}: {{ $value }}
                                @endforeach
                            </small>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price) }} ₫</td>
                        <td>{{ number_format($item->subtotal) }} ₫</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                        <td><strong>{{ number_format($order->final_amount) }} ₫</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="shipping-info">
            <h3>Thông tin giao hàng</h3>
            <p>
                <strong>Người nhận:</strong> {{ $order->shipment->receiver_name }}<br>
                <strong>Điện thoại:</strong> {{ $order->shipment->phone }}<br>
                <strong>Địa chỉ:</strong> {{ $order->shipment->address }}, {{ $order->shipment->ward }}, {{ $order->shipment->district }}, {{ $order->shipment->city }}
            </p>
        </div>

        <div class="footer">
            <p>Cảm ơn bạn đã tin tưởng và mua sắm tại website của chúng tôi.</p>
        </div>
    </div>
</body>
</html>