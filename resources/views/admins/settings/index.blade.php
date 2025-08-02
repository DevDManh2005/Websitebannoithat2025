@extends('admins.layouts.app')

@section('title', 'Cài đặt chung')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Cài đặt chung</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-general" type="button" role="tab">Chung & SEO</button>
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-smtp" type="button" role="tab">Email (SMTP)</button>
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-locale" type="button" role="tab">Ngôn ngữ & Tiền tệ</button>
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab">Liên hệ & Mạng xã hội</button>
                    </div>
                </nav>

                <div class="tab-content pt-4" id="nav-tabContent">
                    {{-- Tab Chung & SEO --}}
                    <div class="tab-pane fade show active" id="nav-general" role="tabpanel">
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Tiêu đề trang web</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" value="{{ $settings['site_name'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="site_description" class="form-label">Mô tả (SEO)</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ $settings['site_description'] ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="site_keywords" class="form-label">Từ khóa (SEO)</label>
                            <input type="text" class="form-control" id="site_keywords" name="site_keywords" value="{{ $settings['site_keywords'] ?? '' }}" placeholder="VD: nội thất, sofa, bàn ghế">
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="logo_light" class="form-label">Logo (dùng trên nền tối)</label>
                                @if(!empty($settings['logo_light']))
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $settings['logo_light']) }}" height="40" class="d-block mb-2 bg-dark p-1 rounded me-3">
                                        <button type="submit" name="remove_image[]" value="logo_light" class="btn btn-sm btn-outline-danger mb-2">Xóa</button>
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="logo_light" name="logo_light" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="logo_dark" class="form-label">Logo (dùng trên nền sáng)</label>
                                @if(!empty($settings['logo_dark']))
                                     <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $settings['logo_dark']) }}" height="40" class="d-block mb-2 border p-1 rounded me-3">
                                        <button type="submit" name="remove_image[]" value="logo_dark" class="btn btn-sm btn-outline-danger mb-2">Xóa</button>
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="logo_dark" name="logo_dark" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="favicon" class="form-label">Favicon</label>
                                @if(!empty($settings['favicon']))
                                     <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $settings['favicon']) }}" height="32" class="d-block mb-2 me-3">
                                        <button type="submit" name="remove_image[]" value="favicon" class="btn btn-sm btn-outline-danger mb-2">Xóa</button>
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="favicon" name="favicon" accept="image/x-icon, image/png, image/svg+xml">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="og_image" class="form-label">Ảnh chia sẻ (OG Image)</label>
                                @if(!empty($settings['og_image']))
                                     <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $settings['og_image']) }}" height="80" class="d-block mb-2 img-thumbnail me-3">
                                        <button type="submit" name="remove_image[]" value="og_image" class="btn btn-sm btn-outline-danger mb-2">Xóa</button>
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="og_image" name="og_image" accept="image/*">
                            </div>
                        </div>
                    </div>

                    {{-- Tab Cấu hình SMTP --}}
                    <div class="tab-pane fade" id="nav-smtp" role="tabpanel">
                        <div class="mb-3"><label for="mail_mailer" class="form-label">Mailer</label><input type="text" class="form-control" id="mail_mailer" name="mail_mailer" value="{{ env('MAIL_MAILER', 'smtp') }}"></div>
                        <div class="mb-3"><label for="mail_host" class="form-label">Host</label><input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ env('MAIL_HOST') }}"></div>
                        <div class="mb-3"><label for="mail_port" class="form-label">Port</label><input type="text" class="form-control" id="mail_port" name="mail_port" value="{{ env('MAIL_PORT') }}"></div>
                        <div class="mb-3"><label for="mail_username" class="form-label">Username</label><input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ env('MAIL_USERNAME') }}"></div>
                        <div class="mb-3"><label for="mail_password" class="form-label">Password</label><input type="password" class="form-control" id="mail_password" name="mail_password" value="{{ env('MAIL_PASSWORD') }}"></div>
                        <div class="mb-3"><label for="mail_encryption" class="form-label">Encryption</label><input type="text" class="form-control" id="mail_encryption" name="mail_encryption" value="{{ env('MAIL_ENCRYPTION') }}"></div>
                        <div class="mb-3"><label for="mail_from_address" class="form-label">From Address</label><input type="email" class="form-control" id="mail_from_address" name="mail_from_address" value="{{ env('MAIL_FROM_ADDRESS') }}"></div>
                        <div class="mb-3"><label for="mail_from_name" class="form-label">From Name</label><input type="text" class="form-control" id="mail_from_name" name="mail_from_name" value="{{ env('MAIL_FROM_NAME') }}"></div>
                    </div>

                    {{-- Tab Ngôn ngữ & Tiền tệ --}}
                    <div class="tab-pane fade" id="nav-locale" role="tabpanel">
                        <p class="text-muted">Chức năng đa ngôn ngữ, đa tiền tệ đang được phát triển.</p>
                    </div>

                    {{-- Tab Liên hệ & Mạng xã hội --}}
                    <div class="tab-pane fade" id="nav-contact" role="tabpanel">
                        <div class="mb-3"><label for="contact_phone" class="form-label">Số điện thoại</label><input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}"></div>
                        <div class="mb-3"><label for="contact_email" class="form-label">Email</label><input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}"></div>
                        <div class="mb-3"><label for="contact_address" class="form-label">Địa chỉ chi tiết</label><input type="text" class="form-control" id="contact_address" name="contact_address" value="{{ $settings['contact_address'] ?? '' }}"></div>
                        <hr>
                        <div class="mb-3"><label for="social_facebook" class="form-label">Facebook URL</label><input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}"></div>
                        <div class="mb-3"><label for="social_instagram" class="form-label">Instagram URL</label><input type="url" class="form-control" id="social_instagram" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}"></div>
                        <div class="mb-3"><label for="social_x" class="form-label">X (Twitter) URL</label><input type="url" class="form-control" id="social_x" name="social_x" value="{{ $settings['social_x'] ?? '' }}"></div>
                        <div class="mb-3"><label for="social_tiktok" class="form-label">Tiktok URL</label><input type="url" class="form-control" id="social_tiktok" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? '' }}"></div>
                    </div>
                </div>
                
                <hr>
                <button type="submit" class="btn btn-primary">Lưu Cài đặt</button>
            </form>
        </div>
    </div>
</div>
@endsection