@extends('layouts.app')

{{-- Đặt tiêu đề cho trang --}}
@section('title', $title ?? 'Chính sách Bảo hành')

{{-- Thêm một chút CSS để cuộn mượt hơn khi bấm vào mục lục --}}
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
            Chúng tôi cam kết mang đến những sản phẩm chất lượng cùng dịch vụ hậu mãi tốt nhất.
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
                <h6 class="text-uppercase fw-bold mb-3">Nội dung chính</h6>
                <nav class="nav nav-pills flex-column">
                    <a class="nav-link toc-link" href="#section1">1. Phạm vi bảo hành</a>
                    <a class="nav-link toc-link" href="#section2">2. Điều kiện bảo hành</a>
                    <a class="nav-link toc-link" href="#section3">3. Trường hợp từ chối</a>
                    <a class="nav-link toc-link" href="#section4">4. Quy trình bảo hành</a>
                </nav>
            </div>
        </div>

        {{-- CỘT BÊN PHẢI: NỘI DUNG CHÍNH --}}
        <div class="col-lg-9">
            <div class="content-section">

                <section id="section1" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-box-seam text-brand me-2"></i>
                        Phạm vi bảo hành
                    </h2>
                    <p class="text-muted lh-lg">
                        Tất cả các sản phẩm được bán ra bởi chúng tôi đều được áp dụng chính sách bảo hành. Thời gian bảo hành được tính từ ngày mua hàng và có chi tiết khác nhau cho từng loại sản phẩm.
                    </p>
                    <ul class="text-muted lh-lg">
                        <li><strong>Sản phẩm nội thất gỗ:</strong> 24 tháng đối với lỗi kỹ thuật của nhà sản xuất.</li>
                        <li><strong>Sản phẩm sofa và nệm:</strong> 12 tháng cho khung và mút.</li>
                        <li><strong>Sản phẩm trang trí:</strong> Không áp dụng bảo hành (vui lòng kiểm tra kỹ khi nhận hàng).</li>
                    </ul>
                </section>

                <hr class="my-5">

                <section id="section2" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-list-check text-brand me-2"></i>
                        Điều kiện bảo hành
                    </h2>
                    <p class="text-muted lh-lg">
                        Để được áp dụng chính sách bảo hành, sản phẩm của quý khách cần đáp ứng các điều kiện sau:
                    </p>
                    <ul class="text-muted lh-lg">
                        <li>Sản phẩm còn trong thời gian bảo hành.</li>
                        <li>Có hóa đơn mua hàng hoặc phiếu bảo hành hợp lệ.</li>
                        <li>Sản phẩm bị lỗi do kỹ thuật của nhà sản xuất, không phải do người dùng gây ra.</li>
                        <li>Tem bảo hành (nếu có) phải còn nguyên vẹn.</li>
                    </ul>
                </section>

                <hr class="my-5">

                <section id="section3" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-x-octagon text-brand me-2"></i>
                        Các trường hợp không được bảo hành
                    </h2>
                    <p class="text-muted lh-lg">
                        Chúng tôi xin phép từ chối bảo hành trong các trường hợp sau:
                    </p>
                    <ul class="text-muted lh-lg">
                        <li>Sản phẩm đã hết thời hạn bảo hành.</li>
                        <li>Hư hỏng do thiên tai, hỏa hoạn, hoặc các yếu tố bên ngoài (ẩm mốc, côn trùng, động vật cắn).</li>
                        <li>Hư hỏng do người dùng sử dụng sai cách, tự ý sửa chữa, hoặc va đập, rơi vỡ.</li>
                        <li>Sản phẩm bị hao mòn tự nhiên trong quá trình sử dụng.</li>
                    </ul>
                </section>

                <hr class="my-5">

                <section id="section4" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-gear text-brand me-2"></i>
                        Quy trình bảo hành
                    </h2>
                    <p class="text-muted lh-lg">
                        Khi có nhu cầu bảo hành sản phẩm, quý khách vui lòng thực hiện theo các bước sau:
                    </p>
                    <ol class="text-muted lh-lg">
                        <li><strong>Bước 1:</strong> Liên hệ với bộ phận chăm sóc khách hàng qua hotline hoặc email để thông báo về tình trạng sản phẩm.</li>
                        <li><strong>Bước 2:</strong> Cung cấp hình ảnh/video về lỗi sản phẩm cùng với hóa đơn mua hàng.</li>
                        <li><strong>Bước 3:</strong> Sau khi xác nhận sản phẩm đủ điều kiện bảo hành, chúng tôi sẽ sắp xếp kỹ thuật viên đến kiểm tra hoặc hướng dẫn quý khách mang sản phẩm đến trung tâm bảo hành gần nhất.</li>
                        <li><strong>Bước 4:</strong> Thời gian xử lý bảo hành từ 7-15 ngày làm việc tùy thuộc vào mức độ phức tạp của lỗi.</li>
                    </ol>
                </section>

            </div>
        </div>
    </div>
</div>
@endsection