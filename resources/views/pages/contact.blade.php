@extends('layouts.app')

@section('title', 'Thông tin liên hệ')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h1 class="display-4 fw-bold text-brand">Liên Hệ Với Chúng Tôi</h1>
        <p class="lead text-muted">Chúng tôi luôn sẵn sàng chào đón và giải đáp mọi thắc mắc của bạn.</p>
    </div>

    <div class="row g-4 g-lg-5 align-items-center">
        {{-- Cột thông tin liên hệ --}}
        <div class="col-lg-5" data-aos="fade-right">
            <div class="card p-4 border-0 shadow-sm h-100">
                <h3 class="fw-bold mb-4">Thông tin</h3>
                <div class="d-flex align-items-start mb-4">
                    <i class="bi bi-geo-alt-fill text-brand fs-4 me-3 mt-1"></i>
                    <div>
                        <strong class="d-block">Địa chỉ Showroom:</strong>
                        123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh
                    </div>
                </div>
                <div class="d-flex align-items-start mb-4">
                    <i class="bi bi-envelope-fill text-brand fs-4 me-3 mt-1"></i>
                    <div>
                        <strong class="d-block">Email:</strong>
                        <a href="mailto:info@yourcompany.com" class="text-decoration-none text-dark">info@yourcompany.com</a>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-4">
                    <i class="bi bi-telephone-fill text-brand fs-4 me-3 mt-1"></i>
                    <div>
                        <strong class="d-block">Hotline:</strong>
                        <a href="tel:+84123456789" class="text-decoration-none text-dark">(+84) 123 456 789</a>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <i class="bi bi-clock-fill text-brand fs-4 me-3 mt-1"></i>
                    <div>
                        <strong class="d-block">Giờ làm việc:</strong>
                        Thứ 2 - Thứ 7: 08:00 - 18:00
                    </div>
                </div>
                 <hr class="my-4">
                <div class="ratio ratio-16x9 rounded shadow-sm">
                    
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.447199147814!2d106.68153531530963!3d10.77698399232079!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3a9d9f1e1b%3A0x7ba2e316d1f30a91!2sB%E1%BA%A3o%20t%C3%A0ng%20Ch%E1%BB%A9ng%20t%C3%ADch%20Chi%E1%BA%BFn%20tranh!5e0!3m2!1svi!2s!4v1678886460987!5m2!1svi!2s" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="rounded"></iframe>
                </div>
            </div>
        </div>

        {{-- Cột hình ảnh nội thất --}}
        <div class="col-lg-7" data-aos="fade-left">
            <div class="row g-3">
                <div class="col-12">
                     
                    <img src="https://via.placeholder.com/600x400" class="img-fluid rounded shadow-lg" alt="Nội thất phòng khách">
                </div>
                <div class="col-6">
                    
                    <img src="https://via.placeholder.com/300x200" class="img-fluid rounded shadow" alt="Nội thất phòng ngủ">
                </div>
                <div class="col-6">
                    
                    <img src="https://via.placeholder.com/300x200" class="img-fluid rounded shadow" alt="Nội thất nhà bếp">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection