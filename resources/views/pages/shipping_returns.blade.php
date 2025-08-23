@extends('layouts.app')

{{-- Đặt tiêu đề cho trang --}}
@section('title', $title ?? 'Giao hàng & Đổi trả')

{{-- CSS để cuộn mượt --}}
@push('styles')
<style>
    html {
        scroll-behavior: smooth;
    }
    .toc-link {
        color: var(--muted);
        transition: all .2s ease;
        border-left: 2px solid transparent;
        padding-left: 1rem;
    }
    .toc-link:hover {
        color: var(--brand);
        border-left-color: var(--brand);
    }
</style>
@endpush


{{-- Phần nội dung chính của trang --}}
@section('content')

{{-- 1. Hero Section --}}
<section class="bg-sand py-5 text-center">
    <div class="container">
        <h1 class="display-5 fw-bold text-brand">{{ $title }}</h1>
        <p class="lead text-muted col-lg-8 mx-auto">
            Thông tin chi tiết về quy trình giao nhận và chính sách đổi trả sản phẩm của chúng tôi.
        </p>
        <p class="text-sm text-muted">Cập nhật lần cuối: {{ date('d/m/Y') }}</p>
    </div>
</section>

{{-- 2. Bố cục hai cột --}}
<div class="container py-5">
    <div class="row">

        {{-- CỘT BÊN TRÁI: MỤC LỤC --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="position-sticky" style="top: 8rem;">
                <h6 class="text-uppercase fw-bold mb-3">Mục Giao Hàng</h6>
                <nav class="nav nav-pills flex-column">
                    <a class="nav-link toc-link" href="#section1">1. Phí vận chuyển</a>
                    <a class="nav-link toc-link" href="#section2">2. Thời gian giao hàng</a>
                </nav>
                <h6 class="text-uppercase fw-bold mb-3 mt-4">Mục Đổi Trả</h6>
                <nav class="nav nav-pills flex-column">
                    <a class="nav-link toc-link" href="#section3">3. Điều kiện đổi trả</a>
                    <a class="nav-link toc-link" href="#section4">4. Quy trình đổi trả</a>
                </nav>
            </div>
        </div>

        {{-- CỘT BÊN PHẢI: NỘI DUNG CHÍNH --}}
        <div class="col-lg-9">
            <div class="content-section">

                <section id="section1" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-truck text-brand me-2"></i>
                        Phí vận chuyển
                    </h2>
                    <p class="text-muted lh-lg">
                        Phí vận chuyển được tính tự động dựa trên địa chỉ nhận hàng của quý khách và đơn vị vận chuyển Giao Hàng Nhanh (GHN).
                    </p>
                    <ul class="text-muted lh-lg">
                        <li><strong>Hỗ trợ phí vận chuyển:</strong> Chúng tôi hỗ trợ 50% phí vận chuyển cho các đơn hàng giá trị cao ở các tỉnh thành khác.</li>
                        <li>Phí vận chuyển chi tiết sẽ được hiển thị rõ ràng ở bước thanh toán.</li>
                    </ul>
                </section>

                <hr class="my-5">

                <section id="section2" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-clock-history text-brand me-2"></i>
                        Thời gian giao hàng
                    </h2>
                    <p class="text-muted lh-lg">
                        Thời gian giao hàng dự kiến được tính từ lúc đơn hàng của quý khách được xác nhận thành công:
                    </p>
                    <ul class="text-muted lh-lg">
                        <li><strong>Nội thành Đà Nẵng :</strong> Từ 1-3 ngày làm việc.</li>
                        <li><strong>Các tỉnh thành khác:</strong> Từ 3-7 ngày làm việc.</li>
                        <li><strong>Lưu ý:</strong> Thời gian có thể kéo dài hơn do các yếu tố bất khả kháng như thiên tai, dịch bệnh hoặc trong các đợt khuyến mãi lớn.</li>
                    </ul>
                </section>

                <hr class="my-5">

                <section id="section3" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-check-all text-brand me-2"></i>
                        Điều kiện đổi trả sản phẩm
                    </h2>
                    <p class="text-muted lh-lg">
                        Chúng tôi hỗ trợ đổi trả sản phẩm trong vòng <strong>7 ngày</strong> kể từ ngày nhận hàng với các điều kiện sau:
                    </p>
                    <ul class="text-muted lh-lg">
                        <li>Sản phẩm bị lỗi kỹ thuật từ nhà sản xuất.</li>
                        <li>Sản phẩm giao không đúng mẫu mã, chủng loại mà khách hàng đã đặt.</li>
                        <li>Sản phẩm còn nguyên vẹn, chưa qua sử dụng, đầy đủ tem mác và bao bì gốc.</li>
                        <li>Có hóa đơn mua hàng hoặc biên nhận giao hàng.</li>
                    </ul>
                </section>

                <hr class="my-5">

                <section id="section4" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-arrow-left-right text-brand me-2"></i>
                        Quy trình đổi trả
                    </h2>
                    <p class="text-muted lh-lg">
                        Để thực hiện đổi trả, quý khách vui lòng làm theo các bước sau:
                    </p>
                    <ol class="text-muted lh-lg">
                        <li><strong>Bước 1:</strong> Liên hệ bộ phận CSKH trong vòng 7 ngày kể từ khi nhận hàng để thông báo yêu cầu đổi trả.</li>
                        <li><strong>Bước 2:</strong> Cung cấp hình ảnh sản phẩm, hóa đơn và nêu rõ lý do cần đổi trả.</li>
                        <li><strong>Bước 3:</strong> Sau khi yêu cầu được xác nhận, quý khách vui lòng đóng gói sản phẩm và gửi về địa chỉ do chúng tôi cung cấp.</li>
                        <li><strong>Bước 4:</strong> Chúng tôi sẽ tiến hành gửi sản phẩm mới để đổi cho quý khách ngay sau khi nhận được sản phẩm trả về và kiểm tra đạt yêu cầu.</li>
                    </ol>
                </section>

            </div>
        </div>
    </div>
</div>
@endsection