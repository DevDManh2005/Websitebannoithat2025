<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Xác nhận đơn hàng #{{ $order->order_code }}</title>
  <style>
    /* Mobile tweak (works in most clients) */
    @media only screen and (max-width:600px){
      .container{ width:100% !important; margin:0 !important; border-radius:0 !important; }
      .px{ padding-left:16px !important; padding-right:16px !important; }
      .col-2, .col-6 { display:block !important; width:100% !important; }
      .text-right { text-align:left !important; }
      .btn { width:100% !important; }
    }
    /* Dark-mode friendly neutrals */
    .text-muted{ color:#6b7280; }
    .text-danger{ color:#dc2626; }
    .text-success{ color:#16a34a; }
    .badge{ display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; line-height:1; }
    .badge-success{ background:#dcfce7; color:#166534; }
    .badge-warning{ background:#fff7ed; color:#9a3412; }
    .table{ width:100%; border-collapse:collapse; }
    .th, .td{ padding:12px; border-bottom:1px solid #e5e7eb; vertical-align:top; font-size:14px; }
    .tfoot .td{ font-weight:600; }
  </style>
</head>
<body style="margin:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;">
    <tr>
      <td align="center" style="padding:24px;">
        <table class="container" role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:600px; background:#ffffff; border-radius:12px; overflow:hidden;">
          <!-- Header -->
          <tr>
            <td class="px" style="padding:24px 32px; background:#111827; color:#ffffff;">
              <table width="100%">
                <tr>
                  <td style="font-size:18px;font-weight:700;">
                    {{ config('app.name', 'Website Bán Nội Thất') }}
                  </td>
                  <td align="right" style="font-size:12px; opacity:.85;">
                    Mã đơn: <strong>#{{ $order->order_code }}</strong>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Greeting -->
          <tr>
            <td class="px" style="padding:24px 32px;">
              <h2 style="margin:0 0 8px; font-size:20px;">Cảm ơn bạn đã đặt hàng! 🎉</h2>
              <p style="margin:0; font-size:14px; line-height:1.6;">
                Chào <strong>{{ $order->user->name }}</strong>, chúng tôi đã nhận được đơn hàng của bạn.
                Dưới đây là thông tin chi tiết:
              </p>

              @php
                $paidOnline = ($order->is_paid ?? false) && (($order->payment_method ?? '') === 'vnpay');
              @endphp
              <div style="margin-top:12px;">
                @if($paidOnline)
                  <span class="badge badge-success">ĐÃ THANH TOÁN (VNPAY)</span>
                @else
                  <span class="badge badge-warning">CHƯA THANH TOÁN (COD)</span>
                @endif
              </div>
            </td>
          </tr>

          <!-- Items Table -->
          <tr>
            <td class="px" style="padding:0 32px 8px;">
              <h3 style="margin:0 0 8px; font-size:16px;">Chi tiết đơn hàng</h3>
              <table class="table">
                <thead>
                  <tr>
                    <th class="th" align="left" style="font-size:12px; text-transform:uppercase; letter-spacing:.02em; color:#6b7280;">Sản phẩm</th>
                    <th class="th" align="center" width="60" style="font-size:12px; text-transform:uppercase; letter-spacing:.02em; color:#6b7280;">SL</th>
                    <th class="th" align="right" width="120" style="font-size:12px; text-transform:uppercase; letter-spacing:.02em; color:#6b7280;">Giá</th>
                    <th class="th" align="right" width="140" style="font-size:12px; text-transform:uppercase; letter-spacing:.02em; color:#6b7280;">Thành tiền</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($order->items as $item)
                  <tr>
                    <td class="td">
                      <div style="font-weight:600;">{{ $item->variant->product->name }}</div>
                      @php
                        $attrs = (array) ($item->variant->attributes ?? []);
                      @endphp
                      @if(!empty($attrs))
                        <div class="text-muted" style="font-size:12px; margin-top:2px;">
                          @foreach($attrs as $key => $value)
                            {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                          @endforeach
                        </div>
                      @endif
                    </td>
                    <td class="td" align="center">{{ $item->quantity }}</td>
                    <td class="td" align="right">{{ number_format($item->price) }} ₫</td>
                    <td class="td" align="right">{{ number_format($item->subtotal) }} ₫</td>
                  </tr>
                @endforeach
                </tbody>
                <tfoot class="tfoot">
                  <tr>
                    <td class="td" colspan="3" align="right" style="font-weight:500;">Tạm tính</td>
                    <td class="td" align="right">{{ number_format($order->total_amount) }} ₫</td>
                  </tr>
                  @if(($order->discount ?? 0) > 0)
                  <tr>
                    <td class="td" colspan="3" align="right" style="font-weight:500;">Giảm giá</td>
                    <td class="td" align="right" style="color:#16a34a;">-{{ number_format($order->discount) }} ₫</td>
                  </tr>
                  @endif
                  <tr>
                    <td class="td" colspan="3" align="right" style="font-weight:500;">Phí vận chuyển</td>
                    <td class="td" align="right">{{ number_format(optional($order->shipment)->shipping_fee ?? 0) }} ₫</td>
                  </tr>
                  <tr>
                    <td class="td" colspan="3" align="right" style="font-size:16px;">Thành tiền</td>
                    <td class="td" align="right" style="font-size:16px; color:#dc2626;"><strong>{{ number_format($order->final_amount) }} ₫</strong></td>
                  </tr>
                </tfoot>
              </table>
            </td>
          </tr>

          <!-- Shipping -->
          <tr>
            <td class="px" style="padding:8px 32px 0;">
              <h3 style="margin:0 0 8px; font-size:16px;">Thông tin giao hàng</h3>
              <table role="presentation" width="100%" style="font-size:14px;">
                <tr>
                  <td class="col-6" style="padding:0 0 12px; width:50%; vertical-align:top;">
                    <div class="text-muted" style="font-size:12px;">Người nhận</div>
                    <div style="font-weight:600;">{{ optional($order->shipment)->receiver_name }}</div>
                    <div class="text-muted">{{ optional($order->shipment)->phone }}</div>
                  </td>
                  <td class="col-6 text-right" style="padding:0 0 12px; width:50%; vertical-align:top; text-align:right;">
                    <div class="text-muted" style="font-size:12px;">Vận chuyển</div>
                    <div style="font-weight:600;">{{ optional($order->shipment)->carrier_name ?? 'GHN' }}</div>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="padding:0;">
                    <div class="text-muted" style="font-size:12px; margin-top:8px;">Địa chỉ</div>
                    <div>
                      {{ optional($order->shipment)->address }},
                      {{ optional($order->shipment)->ward }},
                      {{ optional($order->shipment)->district }},
                      {{ optional($order->shipment)->city }}
                    </div>
                    @if(!empty($order->note))
                      <div class="text-muted" style="font-size:12px; margin-top:8px;">Ghi chú</div>
                      <div>{{ $order->note }}</div>
                    @endif
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- CTA -->
          <tr>
            <td class="px" align="center" style="padding:24px 32px 8px;">
              <a class="btn"
                 href="{{ route('orders.show', $order) }}"
                 style="display:inline-block; background:#111827; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:8px; font-weight:700;">
                Xem đơn hàng
              </a>
              @if(!$paidOnline && in_array($order->status, ['pending','processing']))
                <div style="height:8px;"></div>
                <a class="btn"
                   href="{{ route('payment.vnpay.create', $order) }}"
                   style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:8px; font-weight:700;">
                  Thanh toán online (VNPAY)
                </a>
              @endif
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td class="px" style="padding:16px 32px 28px;">
              <div style="height:1px;background:#e5e7eb;margin:8px 0 16px;"></div>
              <p class="text-muted" style="margin:0; font-size:12px; line-height:1.6;">
                Email được gửi từ hệ thống {{ config('app.name', 'Website Bán Nội Thất') }}.
                Nếu bạn có bất kỳ câu hỏi nào, vui lòng phản hồi email này hoặc liên hệ hỗ trợ.
              </p>
              <p class="text-muted" style="margin:8px 0 0; font-size:12px;">
                © {{ date('Y') }} {{ config('app.name', 'Website Bán Nội Thất') }}. All rights reserved.
              </p>
            </td>
          </tr>
        </table>

        <!-- small gap bottom -->
        <div style="height:24px;"></div>
      </td>
    </tr>
  </table>
</body>
</html>
