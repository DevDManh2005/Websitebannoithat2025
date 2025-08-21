@extends('admins.layouts.app')

@section('title', 'Cài đặt hệ thống')

@section('content')
@php $appUrl = config('app.url'); @endphp
<style>
  .soft-card{border:1px solid rgba(32,25,21,.08);border-radius:16px;box-shadow:0 6px 22px rgba(18,38,63,.06)}
  .soft-card .card-header{background:transparent;border-bottom:1px dashed rgba(32,25,21,.12)}
  .hint{font-size:.875rem;color:#6c757d}
</style>

<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0 fw-bold">Cài đặt hệ thống</h1>
  </div>

  @if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Có lỗi xảy ra:</div>
      <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="card soft-card">
    <div class="card-header"><strong>Cấu hình</strong></div>
    <div class="card-body">
      <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" type="button">Chung & SEO</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-smtp" type="button">Email (SMTP)</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-vnpay" type="button">Thanh toán (VNPAY)</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ghn" type="button">Vận chuyển (GHN)</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-contact" type="button">Liên hệ & MXH</button></li>
        </ul>

        <div class="tab-content pt-4">
          {{-- ===== Chung & SEO ===== --}}
          <div class="tab-pane fade show active" id="tab-general">
            <div class="mb-3">
              <label class="form-label" for="site_name">Tiêu đề trang web</label>
              <input type="text" class="form-control" id="site_name" name="site_name" value="{{ $settings['site_name'] ?? '' }}">
            </div>
            <div class="mb-3">
              <label class="form-label" for="site_description">Mô tả (SEO)</label>
              <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ $settings['site_description'] ?? '' }}</textarea>
            </div>
            <div class="mb-3">
              <label class="form-label" for="site_keywords">Từ khóa (SEO)</label>
              <input type="text" class="form-control" id="site_keywords" name="site_keywords" value="{{ $settings['site_keywords'] ?? '' }}" placeholder="VD: nội thất, sofa, bàn ghế">
            </div>
            <hr>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label" for="logo_light">Logo (nền tối)</label>
                @if(!empty($settings['logo_light']))
                  <div class="d-flex align-items-center">
                    <img src="{{ asset('storage/' . $settings['logo_light']) }}" height="40" class="bg-dark p-1 rounded me-3" alt="logo dark bg">
                    <button type="submit" name="remove_image[]" value="logo_light" class="btn btn-sm btn-outline-danger">Xóa</button>
                  </div>
                @endif
                <input type="file" class="form-control mt-2" id="logo_light" name="logo_light" accept="image/*">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="logo_dark">Logo (nền sáng)</label>
                @if(!empty($settings['logo_dark']))
                  <div class="d-flex align-items-center">
                    <img src="{{ asset('storage/' . $settings['logo_dark']) }}" height="40" class="border p-1 rounded me-3" alt="logo light bg">
                    <button type="submit" name="remove_image[]" value="logo_dark" class="btn btn-sm btn-outline-danger">Xóa</button>
                  </div>
                @endif
                <input type="file" class="form-control mt-2" id="logo_dark" name="logo_dark" accept="image/*">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="favicon">Favicon</label>
                @if(!empty($settings['favicon']))
                  <div class="d-flex align-items-center">
                    <img src="{{ asset('storage/' . $settings['favicon']) }}" height="32" class="me-3" alt="favicon">
                    <button type="submit" name="remove_image[]" value="favicon" class="btn btn-sm btn-outline-danger">Xóa</button>
                  </div>
                @endif
                <input type="file" class="form-control mt-2" id="favicon" name="favicon" accept="image/x-icon,image/png,image/svg+xml">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="og_image">Ảnh chia sẻ (OG Image)</label>
                @if(!empty($settings['og_image']))
                  <div class="d-flex align-items-center">
                    <img src="{{ asset('storage/' . $settings['og_image']) }}" height="80" class="img-thumbnail me-3" alt="og">
                    <button type="submit" name="remove_image[]" value="og_image" class="btn btn-sm btn-outline-danger">Xóa</button>
                  </div>
                @endif
                <input type="file" class="form-control mt-2" id="og_image" name="og_image" accept="image/*">
              </div>
            </div>
          </div>

          {{-- ===== SMTP (tên input tiếng Việt) ===== --}}
          <div class="tab-pane fade" id="tab-smtp">
            <div class="row g-3">
              <div class="col-md-3"><label class="form-label">Loại mailer</label><input type="text" class="form-control" name="smtp_loai" value="{{ env('MAIL_MAILER','smtp') }}"></div>
              <div class="col-md-3"><label class="form-label">Máy chủ (Host)</label><input type="text" class="form-control" name="smtp_host" value="{{ env('MAIL_HOST') }}"></div>
              <div class="col-md-2"><label class="form-label">Cổng (Port)</label><input type="text" class="form-control" name="smtp_cong" value="{{ env('MAIL_PORT') }}"></div>
              <div class="col-md-4"><label class="form-label">Mã hóa</label><input type="text" class="form-control" name="smtp_mahoa" value="{{ env('MAIL_ENCRYPTION') }}"></div>
              <div class="col-md-6"><label class="form-label">Tên đăng nhập</label><input type="text" class="form-control" name="smtp_tendangnhap" value="{{ env('MAIL_USERNAME') }}"></div>
              <div class="col-md-6"><label class="form-label">Mật khẩu</label><input type="password" class="form-control" name="smtp_matkhau" value="{{ env('MAIL_PASSWORD') }}"></div>
              <div class="col-md-6"><label class="form-label">Email gửi</label><input type="email" class="form-control" name="smtp_tu_email" value="{{ env('MAIL_FROM_ADDRESS') }}"></div>
              <div class="col-md-6"><label class="form-label">Tên hiển thị</label><input type="text" class="form-control" name="smtp_tu_ten" value="{{ env('MAIL_FROM_NAME') }}"></div>
            </div>
          </div>

          {{-- ===== VNPAY (tên input tiếng Việt) ===== --}}
          <div class="tab-pane fade" id="tab-vnpay">
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" role="switch" id="bat_vnpay" name="bat_vnpay" value="1" @checked(($settings['bat_vnpay'] ?? false))>
              <label class="form-check-label" for="bat_vnpay">Bật cổng thanh toán VNPAY</label>
            </div>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Đường dẫn thanh toán (VNP_URL)</label><input type="text" class="form-control" name="vnp_duong_dan" value="{{ env('VNP_URL','https://sandbox.vnpayment.vn/paymentv2/vpcpay.html') }}"></div>
              <div class="col-md-6"><label class="form-label">Đường dẫn API (VNP_API_URL)</label><input type="text" class="form-control" name="vnp_api" value="{{ env('VNP_API_URL','https://sandbox.vnpayment.vn/merchant_webapi') }}"></div>
              <div class="col-md-6"><label class="form-label">Mã website (VNP_TMNCODE)</label><input type="text" class="form-control" name="vnp_ma_website" value="{{ env('VNP_TMNCODE') }}"></div>
              <div class="col-md-6"><label class="form-label">Chuỗi bí mật (VNP_HASHSECRET)</label><input type="text" class="form-control" name="vnp_bi_mat" value="{{ env('VNP_HASHSECRET') }}"></div>
              <div class="col-md-6">
                <label class="form-label">Return URL</label>
                <input type="text" class="form-control" name="vnp_return_url" value="{{ env('VNP_RETURNURL', rtrim($appUrl,'/').'/payment/vnpay-return') }}">
                <div class="hint">URL người dùng quay về sau thanh toán.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">IPN URL</label>
                <input type="text" class="form-control" name="vnp_ipn_url" value="{{ env('VNP_IPN_URL', rtrim($appUrl,'/').'/payment/vnpay-ipn') }}">
                <div class="hint">Endpoint nhận IPN từ VNPAY (POST).</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Ngân hàng mặc định (tùy chọn)</label>
                <input type="text" class="form-control" name="vnp_ngan_hang_mac_dinh" value="{{ env('VNP_BANKCODE','NCB') }}">
                <div class="hint">Để trống nếu cho phép người dùng chọn ngân hàng trên trang VNPAY.</div>
              </div>
            </div>
          </div>

          {{-- ===== GHN (tên input tiếng Việt) ===== --}}
          <div class="tab-pane fade" id="tab-ghn">
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" role="switch" id="bat_ghn" name="bat_ghn" value="1" @checked(($settings['bat_ghn'] ?? false))>
              <label class="form-check-label" for="bat_ghn">Bật tích hợp GHN</label>
            </div>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Token (GHN_TOKEN)</label><input type="text" class="form-control" name="ghn_token" value="{{ env('GHN_TOKEN') }}"></div>
              <div class="col-md-3"><label class="form-label">Shop ID (GHN_SHOP_ID)</label><input type="text" class="form-control" name="ghn_shop_id" value="{{ env('GHN_SHOP_ID') }}"></div>
              <div class="col-md-3"><label class="form-label">API URL</label><input type="text" class="form-control" name="ghn_api" value="{{ env('GHN_API_URL','https://online-gateway.ghn.vn/shiip/public-api') }}"></div>

              <div class="col-md-6"><label class="form-label">Tên người gửi</label><input type="text" class="form-control" name="ghn_ten_nguoi_gui" value="{{ env('GHN_PICK_NAME') }}"></div>
              <div class="col-md-6"><label class="form-label">SĐT người gửi</label><input type="text" class="form-control" name="ghn_sdt_nguoi_gui" value="{{ env('GHN_PICK_TEL') }}"></div>

              <div class="col-md-12"><label class="form-label">Địa chỉ lấy hàng</label><input type="text" class="form-control" name="ghn_dia_chi_nhan" value="{{ env('GHN_PICK_ADDRESS') }}"></div>

              <div class="col-md-4"><label class="form-label">Tỉnh/Thành</label><input type="text" class="form-control" name="ghn_tinh_thanh" value="{{ env('GHN_PICK_PROVINCE') }}"></div>
              <div class="col-md-4"><label class="form-label">Quận/Huyện</label><input type="text" class="form-control" name="ghn_quan_huyen" value="{{ env('GHN_PICK_DISTRICT') }}"></div>
              <div class="col-md-4"><label class="form-label">Mã Quận/Huyện</label><input type="text" class="form-control" name="ghn_ma_quan_huyen" value="{{ env('GHN_PICK_DISTRICT_ID') }}"></div>

              <div class="col-md-4"><label class="form-label">Mã Phường/Xã</label><input type="text" class="form-control" name="ghn_phuong_xa_code" value="{{ env('GHN_PICK_WARD_CODE') }}"></div>
              <div class="col-md-4"><label class="form-label">Ngưỡng nặng (gram)</label><input type="text" class="form-control" name="ghn_can_nang_nguong" value="{{ env('GHN_MIN_HEAVY_WEIGHT',1000) }}"></div>
            </div>
          </div>

          {{-- ===== Liên hệ & MXH ===== --}}
          <div class="tab-pane fade" id="tab-contact">
            <div class="row g-3">
              <div class="col-md-4"><label class="form-label">Số điện thoại</label><input type="text" class="form-control" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}"></div>
              <div class="col-md-4"><label class="form-label">Email</label><input type="email" class="form-control" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}"></div>
              <div class="col-md-4"><label class="form-label">Địa chỉ chi tiết</label><input type="text" class="form-control" name="contact_address" value="{{ $settings['contact_address'] ?? '' }}"></div>
              <div class="col-md-6"><label class="form-label">Facebook URL</label><input type="url" class="form-control" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}"></div>
              <div class="col-md-6"><label class="form-label">Instagram URL</label><input type="url" class="form-control" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}"></div>
              <div class="col-md-6"><label class="form-label">X (Twitter) URL</label><input type="url" class="form-control" name="social_x" value="{{ $settings['social_x'] ?? '' }}"></div>
              <div class="col-md-6"><label class="form-label">Tiktok URL</label><input type="url" class="form-control" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? '' }}"></div>
            </div>
          </div>
        </div>

        <hr class="mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Lưu cài đặt</button>
      </form>
    </div>
  </div>
</div>
@endsection
