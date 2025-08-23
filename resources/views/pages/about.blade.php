{{-- Kế thừa layout chính từ layouts.app --}}
@extends('layouts.app')

{{-- Đặt tiêu đề cho trang --}}
@section('title', 'Giới thiệu về chúng tôi')

{{-- Phần nội dung chính của trang --}}
@section('content')
<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
            <h1 class="display-4 fw-bold text-brand">Về Chúng Tôi</h1>
            <p class="lead text-muted">
                Chào mừng bạn đến với [Tên công ty/cửa hàng của bạn]. Chúng tôi tự hào là đơn vị cung cấp các giải pháp nội thất hàng đầu, mang đến không gian sống đẳng cấp và tiện nghi cho mọi gia đình.
            </p>
            <p>
                Với nhiều năm kinh nghiệm trong ngành, đội ngũ của chúng tôi luôn nỗ lực không ngừng để tạo ra những sản phẩm chất lượng cao, thiết kế tinh tế và phù hợp với xu hướng hiện đại.
            </p>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
            
            <img src="https://www.lanha.vn/wp-content/uploads/2023/06/thiet-ke-noi-that-nha-pho-32.jpeg.webp" alt="Không gian nội thất hiện đại" class="img-fluid rounded shadow-lg">
        </div>
    </div>

    <hr class="my-5">

    <div class="row text-center">
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card h-100 border-0 shadow-sm p-4">
                <div class="card-body">
                    <i class="fas fa-bullseye fa-3x text-brand mb-3"></i>
                    <h4 class="fw-bold">Sứ Mệnh</h4>
                    <p class="text-muted">Kiến tạo không gian sống hoàn hảo, nâng cao chất lượng cuộc sống cho khách hàng thông qua những sản phẩm nội thất ưu việt.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card h-100 border-0 shadow-sm p-4">
                <div class="card-body">
                    <i class="fas fa-eye fa-3x text-brand mb-3"></i>
                    <h4 class="fw-bold">Tầm Nhìn</h4>
                    <p class="text-muted">Trở thành thương hiệu nội thất được tin yêu và lựa chọn hàng đầu tại Việt Nam, tiên phong trong việc đổi mới và sáng tạo.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card h-100 border-0 shadow-sm p-4">
                <div class="card-body">
                    <i class="fas fa-gem fa-3x text-brand mb-3"></i>
                    <h4 class="fw-bold">Giá Trị Cốt Lõi</h4>
                    <p class="text-muted">Chất lượng - Sáng tạo - Tận tâm - Chuyên nghiệp. Chúng tôi cam kết mang lại sự hài lòng tuyệt đối cho mọi khách hàng.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection