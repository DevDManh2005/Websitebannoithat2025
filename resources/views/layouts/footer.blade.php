<footer class="bg-dark text-white pt-5 pb-4">
    <div class="container text-center text-md-start">
        <div class="row">
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4">
                    {{ $settings['site_name'] ?? 'Website Nội Thất' }}
                </h6>
                <p>{{ $settings['site_description'] ?? 'Chuyên cung cấp các sản phẩm nội thất cao cấp, mang lại không gian sống đẳng cấp và tiện nghi.' }}</p>
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4">Sản phẩm</h6>
                <p><a href="#!" class="text-reset">Sofa</a></p>
                <p><a href="#!" class="text-reset">Bàn ăn</a></p>
                <p><a href="#!" class="text-reset">Giường ngủ</a></p>
            </div>

            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                <h6 class="text-uppercase fw-bold mb-4">Liên kết hữu ích</h6>
                <p><a href="{{ route('orders.index') }}" class="text-reset">Đơn hàng</a></p>
                <p><a href="#" class="text-reset">Chính sách</a></p>
                <p><a href="#" class="text-reset">Hỗ trợ</a></p>
            </div>

            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                <h6 class="text-uppercase fw-bold mb-4">Liên hệ</h6>
                <p><i class="bi bi-geo-alt-fill me-3"></i> {{ $settings['contact_address'] ?? '123 Đường ABC, Đà Nẵng' }}</p>
                <p><i class="bi bi-envelope-fill me-3"></i> {{ $settings['contact_email'] ?? 'info@example.com' }}</p>
                <p><i class="bi bi-telephone-fill me-3"></i> {{ $settings['contact_phone'] ?? '+ 01 234 567 88' }}</p>
            </div>
        </div>
    </div>
</footer>
<div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
    © {{ date('Y') }} Copyright:
    <a class="text-white" href="{{ url('/') }}">{{ $settings['site_name'] ?? 'Website Nội Thất' }}</a>
</div>