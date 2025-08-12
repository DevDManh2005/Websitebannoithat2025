<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mã OTP khôi phục mật khẩu</title>
  <style>
    @media only screen and (max-width:600px){
      .container{ width:100% !important; margin:0 !important; border-radius:0 !important; }
      .px{ padding-left:16px !important; padding-right:16px !important; }
      .btn{ width:100% !important; }
    }
  </style>
</head>
<body style="margin:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;">
    <tr>
      <td align="center" style="padding:24px;">
        <table class="container" role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
          <!-- Header -->
          <tr>
            <td class="px" style="padding:20px 28px;background:#111827;color:#ffffff;">
              <table width="100%">
                <tr>
                  <td style="font-size:18px;font-weight:700;">
                    {{ config('app.name', 'Ứng dụng của bạn') }}
                  </td>
                  <td align="right" style="font-size:12px;opacity:.85;">
                    Bảo mật OTP
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td class="px" style="padding:24px 28px;">
              <h2 style="margin:0 0 8px;font-size:20px;">Khôi phục mật khẩu</h2>
              <p style="margin:0 0 12px;font-size:14px;line-height:1.6;">
                Xin chào, chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.
                Vui lòng sử dụng <strong>mã OTP</strong> bên dưới để tiếp tục.
              </p>

              <!-- OTP box -->
              <div style="margin:16px 0;padding:16px;border:1px dashed #e5e7eb;border-radius:10px;text-align:center;background:#fafafa;">
                <div style="font-size:12px;color:#6b7280;margin-bottom:4px;">Mã OTP</div>
                <div style="font-size:28px;letter-spacing:4px;font-weight:800;font-family:Consolas,Monaco,monospace;">
                  {{ $code }}
                </div>
                <div style="font-size:12px;color:#6b7280;margin-top:6px;">
                  Hiệu lực trong {{ config('otp.expire_minutes', env('OTP_EXPIRE_MINUTES', 10)) }} phút.
                </div>
              </div>

              <p style="margin:0 0 8px;font-size:14px;line-height:1.6;">
                Nếu bạn <strong>không thực hiện</strong> yêu cầu này, hãy bỏ qua email hoặc liên hệ hỗ trợ để được trợ giúp.
              </p>

              <!-- Optional CTA (nếu có trang reset kèm token riêng bạn có thể truyền vào) -->
              {{-- 
              <div style="margin-top:16px;" align="center">
                <a class="btn" href="{{ $resetUrl ?? '#' }}" 
                   style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:8px;font-weight:700;">
                  Mở trang đặt lại mật khẩu
                </a>
              </div>
              --}}

              <div style="height:8px;"></div>
              <p style="margin:0;font-size:12px;color:#6b7280;">
                Lưu ý: Không chia sẻ mã OTP cho bất kỳ ai. {{ config('app.name', 'Ứng dụng của bạn') }} không bao giờ yêu cầu bạn cung cấp OTP qua điện thoại/chat.
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td class="px" style="padding:16px 28px 28px;">
              <div style="height:1px;background:#e5e7eb;margin:8px 0 16px;"></div>
              <p style="margin:0;font-size:12px;color:#6b7280;">
                Email được gửi tự động từ {{ config('app.name', 'Ứng dụng của bạn') }} –
                vui lòng không trả lời trực tiếp. Cần hỗ trợ? Hãy phản hồi qua địa chỉ
                <a href="mailto:{{ config('mail.from.address') }}" style="color:#2563eb;text-decoration:none;">
                  {{ config('mail.from.address') }}
                </a>.
              </p>
              <p style="margin:8px 0 0;font-size:12px;color:#9ca3af;">
                © {{ date('Y') }} {{ config('app.name', 'Ứng dụng của bạn') }}. All rights reserved.
              </p>
            </td>
          </tr>
        </table>
        <div style="height:24px;"></div>
      </td>
    </tr>
  </table>
</body>
</html>
