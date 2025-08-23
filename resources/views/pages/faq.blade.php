@extends('layouts.app')

{{-- Đặt tiêu đề cho trang --}}
@section('title', $title ?? 'Câu hỏi thường gặp')

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
    /* Chỉnh sửa nhỏ cho accordion */
    .accordion-button:not(.collapsed) {
        background-color: var(--sand);
        color: var(--brand);
        box-shadow: none;
    }
    .accordion-item {
        border-radius: var(--radius) !important;
        border: 1px solid rgba(0,0,0,.08);
        overflow: hidden;
    }
    .accordion-header {
        border-radius: 0;
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
            Giải đáp các thắc mắc phổ biến về sản phẩm, dịch vụ và chính sách của chúng tôi.
        </p>
    </div>
</section>

{{-- 2. Bố cục hai cột --}}
<div class="container py-5">
    <div class="row">

        {{-- CỘT BÊN TRÁI: MỤC LỤC --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="position-sticky" style="top: 8rem;">
                <h6 class="text-uppercase fw-bold mb-3">Chủ đề</h6>
                <nav class="nav nav-pills flex-column">
                    <a class="nav-link toc-link" href="#cat1">1. Về sản phẩm</a>
                    <a class="nav-link toc-link" href="#cat2">2. Đặt hàng & Thanh toán</a>
                    <a class="nav-link toc-link" href="#cat3">3. Vận chuyển</a>
                </nav>
            </div>
        </div>

        {{-- CỘT BÊN PHẢI: NỘI DUNG CÂU HỎI --}}
        <div class="col-lg-9">
            
            {{-- Nhóm câu hỏi 1 --}}
            <section id="cat1" class="mb-5">
                <h2 class="h3 fw-bold mb-4">
                    <i class="bi bi-box-seam text-brand me-2"></i>
                    Về sản phẩm
                </h2>
                <div class="accordion" id="faqProduct">
                    {{-- Câu hỏi 1.1 --}}
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading1_1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1_1" aria-expanded="false" aria-controls="collapse1_1">
                                Sản phẩm có được làm từ gỗ thật không?
                            </button>
                        </h2>
                        <div id="collapse1_1" class="accordion-collapse collapse" aria-labelledby="heading1_1" data-bs-parent="#faqProduct">
                            <div class="accordion-body text-muted">
                                Hầu hết các sản phẩm của chúng tôi được làm từ gỗ tự nhiên đã qua xử lý như gỗ sồi, gỗ óc chó. Một số sản phẩm có thể kết hợp gỗ công nghiệp MDF cao cấp để đảm bảo độ bền và tối ưu giá thành. Chi tiết vật liệu luôn được ghi rõ trong mô tả của từng sản phẩm.
                            </div>
                        </div>
                    </div>
                    {{-- Câu hỏi 1.2 --}}
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading1_2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1_2" aria-expanded="false" aria-controls="collapse1_2">
                                Tôi có thể yêu cầu tùy chỉnh kích thước hoặc màu sắc không?
                            </button>
                        </h2>
                        <div id="collapse1_2" class="accordion-collapse collapse" aria-labelledby="heading1_2" data-bs-parent="#faqProduct">
                            <div class="accordion-body text-muted">
                                Có, chúng tôi nhận tùy chỉnh một số sản phẩm theo yêu cầu. Vui lòng liên hệ trực tiếp với đội ngũ tư vấn của chúng tôi qua hotline hoặc email để được hỗ trợ chi tiết về khả năng tùy chỉnh và báo giá.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Nhóm câu hỏi 2 --}}
            <section id="cat2" class="mb-5">
                <h2 class="h3 fw-bold mb-4">
                    <i class="bi bi-credit-card text-brand me-2"></i>
                    Đặt hàng & Thanh toán
                </h2>
                <div class="accordion" id="faqPayment">
                    {{-- Câu hỏi 2.1 --}}
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading2_1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2_1" aria-expanded="false" aria-controls="collapse2_1">
                                Tôi có thể thanh toán bằng những hình thức nào?
                            </button>
                        </h2>
                        <div id="collapse2_1" class="accordion-collapse collapse" aria-labelledby="heading2_1" data-bs-parent="#faqPayment">
                            <div class="accordion-body text-muted">
                                Chúng tôi chấp nhận các hình thức thanh toán sau:
                                <ul>
                                    <li>Thanh toán khi nhận hàng (COD).</li>
                                    <li>Chuyển khoản ngân hàng.</li>
                                    <li>Thanh toán qua ví điện tử (Momo, ZaloPay).</li>
                                    <li>Thanh toán qua thẻ Visa/Mastercard.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    {{-- Câu hỏi 2.2 --}}
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading2_2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2_2" aria-expanded="false" aria-controls="collapse2_2">
                                Làm thế nào để sử dụng mã giảm giá?
                            </button>
                        </h2>
                        <div id="collapse2_2" class="accordion-collapse collapse" aria-labelledby="heading2_2" data-bs-parent="#faqPayment">
                            <div class="accordion-body text-muted">
                                Bạn có thể nhập mã giảm giá vào ô "Mã khuyến mãi" ở trang giỏ hàng hoặc trang thanh toán. Hệ thống sẽ tự động trừ số tiền được giảm vào tổng giá trị đơn hàng của bạn.
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            {{-- Nhóm câu hỏi 3 --}}
            <section id="cat3" class="mb-5">
                <h2 class="h3 fw-bold mb-4">
                    <i class="bi bi-truck text-brand me-2"></i>
                    Vận chuyển
                </h2>
                <div class="accordion" id="faqShipping">
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading3_1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3_1" aria-expanded="false" aria-controls="collapse3_1">
                                Phí vận chuyển được tính như thế nào?
                            </button>
                        </h2>
                        <div id="collapse3_1" class="accordion-collapse collapse" aria-labelledby="heading3_1" data-bs-parent="#faqShipping">
                            <div class="accordion-body text-muted">
                                Phí vận chuyển được tính tự động dựa trên địa chỉ nhận hàng của bạn và khối lượng sản phẩm. Chúng tôi có chính sách miễn phí vận chuyển cho các đơn hàng giá trị cao tại một số khu vực. Vui lòng xem chi tiết tại trang <a href="{{ route('shipping_returns.show') }}">Chính sách Giao hàng & Đổi trả</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
@endsection