@extends('layouts.app')

{{-- Đặt tiêu đề cho trang --}}
@section('title', $title ?? 'Điều khoản & Dịch vụ')

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
            Vui lòng đọc kỹ các điều khoản và điều kiện của chúng tôi trước khi sử dụng dịch vụ.
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
                    <a class="nav-link toc-link" href="#section1">1. Chấp nhận Điều khoản</a>
                    <a class="nav-link toc-link" href="#section2">2. Thay đổi Điều khoản</a>
                    <a class="nav-link toc-link" href="#section3">3. Quyền sở hữu trí tuệ</a>
                    <a class="nav-link toc-link" href="#section4">4. Tài khoản người dùng</a>
                    <a class="nav-link toc-link" href="#section5">5. Chấm dứt</a>
                </nav>
            </div>
        </div>

        {{-- CỘT BÊN PHẢI: NỘI DUNG CHÍNH --}}
        <div class="col-lg-9">
            <div class="content-section">

                <section id="section1" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-check2-circle text-brand me-2"></i>
                        Chấp nhận Điều khoản
                    </h2>
                    <p class="text-muted lh-lg">
                        Bằng việc truy cập và sử dụng trang web của chúng tôi, bạn đồng ý tuân thủ và bị ràng buộc bởi các điều khoản và điều kiện dịch vụ này. Nếu bạn không đồng ý với bất kỳ phần nào của các điều khoản, bạn không được phép sử dụng dịch vụ của chúng tôi.
                    </p>
                    <p class="text-muted lh-lg">
                        Việc sử dụng dịch vụ đồng nghĩa với việc bạn xác nhận đã đọc, hiểu và chấp nhận toàn bộ các quy định được nêu ra.
                    </p>
                </section>

                <hr class="my-5">

                <section id="section2" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-arrow-repeat text-brand me-2"></i>
                        Thay đổi Điều khoản
                    </h2>
                    <p class="text-muted lh-lg">
                        Chúng tôi có quyền sửa đổi hoặc thay thế các Điều khoản này vào bất kỳ lúc nào theo quyết định riêng của mình. Chúng tôi sẽ thông báo cho bạn về bất kỳ thay đổi nào bằng cách đăng các điều khoản mới trên trang web này. Việc bạn tiếp tục sử dụng Dịch vụ sau khi các thay đổi có hiệu lực có nghĩa là bạn chấp nhận các điều khoản đã được sửa đổi.
                    </p>
                </section>

                <hr class="my-5">

                <section id="section3" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-shield-check text-brand me-2"></i>
                        Quyền sở hữu trí tuệ
                    </h2>
                    <p class="text-muted lh-lg">
                        Dịch vụ và tất cả nội dung, tính năng và chức năng gốc của nó là và sẽ vẫn là tài sản độc quyền của công ty chúng tôi và các bên cấp phép của nó. Dịch vụ được bảo vệ bởi bản quyền, nhãn hiệu và các luật khác của cả Việt Nam và nước ngoài.
                    </p>
                </section>

                <hr class="my-5">

                <section id="section4" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-person-gear text-brand me-2"></i>
                        Tài khoản người dùng
                    </h2>
                    <p class="text-muted lh-lg">
                        Khi bạn tạo một tài khoản với chúng tôi, bạn phải cung cấp cho chúng tôi thông tin chính xác, đầy đủ và hiện hành tại mọi thời điểm. Việc không làm như vậy cấu thành một sự vi phạm các Điều khoản, có thể dẫn đến việc chấm dứt ngay lập tức tài khoản của bạn trên Dịch vụ của chúng tôi.
                    </p>
                </section>

                <hr class="my-5">

                <section id="section5" class="mb-5">
                    <h2 class="h3 fw-bold mb-3">
                        <i class="bi bi-slash-circle text-brand me-2"></i>
                        Chấm dứt
                    </h2>
                    <p class="text-muted lh-lg">
                        Chúng tôi có thể chấm dứt hoặc đình chỉ quyền truy cập của bạn vào Dịch vụ của chúng tôi ngay lập tức, mà không cần thông báo trước hoặc chịu trách nhiệm pháp lý, vì bất kỳ lý do gì, bao gồm nhưng không giới hạn nếu bạn vi phạm các Điều khoản.
                    </p>
                </section>

            </div>
        </div>
    </div>
</div>
@endsection