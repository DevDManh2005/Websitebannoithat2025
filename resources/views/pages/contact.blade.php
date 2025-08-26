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
                            <strong class="d-block">Địa chỉ :</strong>
                            Đường Số 5 , Hòa Hiệp Nam , Liên Chiểu , Đà Nẵng
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-envelope-fill text-brand fs-4 me-3 mt-1"></i>
                        <div>
                            <strong class="d-block">Email:</strong>
                            <a href="mailto:info@yourcompany.com"
                                class="text-decoration-none text-dark">Manhldpd10554@gmail.com</a>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-telephone-fill text-brand fs-4 me-3 mt-1"></i>
                        <div>
                            <strong class="d-block">Hotline:</strong>
                            <a href="tel:+84123456789" class="text-decoration-none text-dark">(+84) 865024471</a>
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
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3834.2636238166824!2d108.16712607589218!3d16.05180403990268!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3142191680ef7f49%3A0x7e73d5e29a155cbc!2zMTIwIE5ndXnhu4VuIEh1eSBUxrDhu59uZywgSG_DoCBBbiwgTGnDqm4gQ2hp4buDdSwgxJDDoCBO4bq1bmcgNTUwMDAwLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1756180496547!5m2!1svi!2s"
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            class="rounded"></iframe>

                    </div>
                </div>
            </div>

            {{-- Cột hình ảnh nội thất --}}
            <div class="col-lg-7" data-aos="fade-left">
                <div class="row g-3">
                    <div class="col-12">

                        <img src="https://media.loveitopcdn.com/39292/thiet-ke-noi-that-phong-khach-go-mdf-dep-hien-dai-dang-cap-02.jpg"
                            class="img-fluid rounded shadow-lg" alt="Nội thất phòng khách">
                    </div>
                    <div class="col-6">

                        <img src="https://www.lanha.vn/wp-content/uploads/2023/06/thiet-ke-noi-that-phong-ngu-10.jpeg.webp"
                            class="img-fluid rounded shadow" alt="Nội thất phòng ngủ">
                    </div>
                    <div class="col-6">

                        <img src="https://kinhphucdat.com/wp-content/uploads/2024/08/top-99-mau-nha-bep-dep-hot-nhat-2024.png"
                            class="img-fluid rounded shadow" alt="Nội thất nhà bếp">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection