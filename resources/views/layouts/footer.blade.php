<footer class="bg-dark text-light pt-5 mt-5">
    {{-- ======= THÂN FOOTER ======= --}}
    <section class="border-top border-secondary-subtle">
        <div class="container py-5">
            <div class="row g-4">
                {{-- Cột 1: Thông tin cửa hàng --}}
                <div class="col-12 col-lg-4">
                    <a href="{{ route('home') }}" class="d-inline-block mb-3">
                        @php
                            $logo = $settings['logo_dark'] ?? ($settings['logo_light'] ?? null);
                        @endphp
                        @if($logo)
                            <img src="{{ asset('storage/' . $logo) }}"
                                 alt="{{ $settings['site_name'] ?? config('app.name', 'EternaHome') }}"
                                 style="height:58px" loading="lazy">
                        @else
                            <span class="fs-4 fw-bold text-light">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                        @endif
                    </a>
                    <p class="text-secondary mb-3">
                        {{ $settings['footer_about'] ?? 'EternaHome – Nội thất tinh gọn, tối ưu không gian sống của bạn.' }}
                    </p>

                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="bi bi-geo-alt me-2 text-warning"></i>
                            {{ $settings['contact_address'] ?? 'Địa chỉ liên hệ' }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2 text-warning"></i>
                            <a class="link-light link-underline-opacity-0" href="tel:{{ preg_replace('/\s+/', '', $settings['contact_phone'] ?? '') }}">
                                {{ $settings['contact_phone'] ?? '1900 1234' }}
                            </a>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope me-2 text-warning"></i>
                            <a class="link-light link-underline-opacity-0" href="mailto:{{ $settings['contact_email'] ?? 'hello@example.com' }}">
                                {{ $settings['contact_email'] ?? 'hello@example.com' }}
                            </a>
                        </li>
                        @if(!empty($settings['business_hours']))
                            <li class="mb-2">
                                <i class="bi bi-clock me-2 text-warning"></i>
                                {{ $settings['business_hours'] }}
                            </li>
                        @endif
                    </ul>

                    {{-- Social --}}
                    <div class="d-flex align-items-center gap-3 mt-3">
                        @php
                            $fb = $settings['social_facebook'] ?? null;
                            $yt = $settings['social_youtube'] ?? null;
                            $tt = $settings['social_tiktok'] ?? null;
                            $ig = $settings['social_instagram'] ?? null;
                            $zl = $settings['social_zalo'] ?? null;
                        @endphp
                        @if($fb)<a href="{{ $fb }}" target="_blank" class="text-secondary" aria-label="Facebook"><i class="bi bi-facebook fs-4"></i></a>@endif
                        @if($ig)<a href="{{ $ig }}" target="_blank" class="text-secondary" aria-label="Instagram"><i class="bi bi-instagram fs-4"></i></a>@endif
                        @if($yt)<a href="{{ $yt }}" target="_blank" class="text-secondary" aria-label="YouTube"><i class="bi bi-youtube fs-4"></i></a>@endif
                        @if($tt)<a href="{{ $tt }}" target="_blank" class="text-secondary" aria-label="TikTok"><i class="bi bi-tiktok fs-4"></i></a>@endif
                        @if($zl)<a href="{{ $zl }}" target="_blank" class="text-secondary" aria-label="Zalo"><i class="bi bi-chat-dots fs-4"></i></a>@endif
                    </div>
                </div>

                {{-- Cột 2: Danh mục nổi bật / liên kết nhanh --}}
                <div class="col-6 col-lg-2">
                    <h6 class="text-uppercase text-secondary fw-semibold mb-3">Danh mục</h6>
                    <ul class="list-unstyled small">
                        @php
                            // bạn có thể truyền $footerCategories từ Composer/share view
                            // mỗi item: ['name' => 'Sofa', 'url' => route('products.index', ['category' => 'sofa'])]
                            $footerCategories = $footerCategories ?? null;
                        @endphp

                        @if(is_iterable($footerCategories) && count($footerCategories))
                            @foreach($footerCategories as $cat)
                                <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ $cat['url'] ?? '#' }}">{{ $cat['name'] ?? 'Danh mục' }}</a></li>
                            @endforeach
                        @else
                            <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ route('products.index', ['q' => 'Sofa']) }}">Sofa & Ghế</a></li>
                            <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ route('products.index', ['q' => 'Bàn']) }}">Bàn</a></li>
                            <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ route('products.index', ['q' => 'Tủ']) }}">Tủ – Kệ</a></li>
                            <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ route('products.index', ['q' => 'Giường']) }}">Giường ngủ</a></li>
                            <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ route('products.index', ['q' => 'Decor']) }}">Decor</a></li>
                        @endif
                    </ul>
                </div>

                {{-- Cột 3: Hỗ trợ khách hàng --}}
                <div class="col-6 col-lg-3">
                    <h6 class="text-uppercase text-secondary fw-semibold mb-3">Hỗ trợ</h6>
                    <ul class="list-unstyled small">
                        @php
                            $policyRoute  = Route::has('policy') ? route('policy') : '#';
                            $warrantyRoute= Route::has('warranty') ? route('warranty') : '#';
                            $shippingRoute= Route::has('shipping.policy') ? route('shipping.policy') : '#';
                            $paymentRoute = Route::has('payment.methods') ? route('payment.methods') : '#';
                            $contactRoute = Route::has('contact') ? route('contact') : '#';
                            $faqRoute     = Route::has('faq') ? route('faq') : '#';
                        @endphp
                        <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ $policyRoute }}">Điều khoản & Chính sách</a></li>
                        <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ $warrantyRoute }}">Bảo hành & Lắp đặt</a></li>
                        <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ $shippingRoute }}">Giao hàng & Đổi trả</a></li>
                        <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ $paymentRoute }}">Hình thức thanh toán</a></li>
                        <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ $faqRoute }}">Câu hỏi thường gặp</a></li>
                        <li class="mb-2"><a class="link-light link-underline-opacity-0" href="{{ $contactRoute }}">Liên hệ</a></li>
                    </ul>
                </div>

                {{-- Cột 4: Newsletter & cửa hàng --}}
                <div class="col-12 col-lg-3">
                    <h6 class="text-uppercase text-secondary fw-semibold mb-3">Nhận ưu đãi</h6>
                    <p class="text-secondary small">Đăng ký nhận bản tin để cập nhật bộ sưu tập mới & voucher độc quyền.</p>

                    @php $nlRoute = Route::has('newsletter.subscribe') ? route('newsletter.subscribe') : null; @endphp
                    <form id="newsletter-form" action="{{ $nlRoute ?? '#' }}" method="POST" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="Email của bạn" required>
                            <button class="btn btn-warning btn-sm" type="submit">Đăng ký</button>
                        </div>
                        <div class="form-text text-danger d-none" id="newsletter-error"></div>
                        <div class="form-text text-success d-none" id="newsletter-success">Đăng ký thành công!</div>
                    </form>

                    @if(!empty($settings['showroom_embed']))
                        <div class="ratio ratio-16x9 rounded overflow-hidden">
                            {!! $settings['showroom_embed'] !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ======= CHÂN BOTTOM ======= --}}
    <section class="border-top border-secondary-subtle py-3">
        <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
            <div class="small text-secondary order-2 order-lg-1">
                © {{ now()->year }} {{ $settings['site_name'] ?? config('app.name', 'EternaHome') }}. All rights reserved.
            </div>

            {{-- Logo phương thức thanh toán (minh hoạ) --}}
            <div class="order-3 order-lg-2">
                <span class="badge bg-secondary-subtle text-light me-1"><i class="bi bi-credit-card-2-front"></i></span>
                <span class="badge bg-secondary-subtle text-light me-1"><i class="bi bi-wallet2"></i></span>
                <span class="badge bg-secondary-subtle text-light"><i class="bi bi-bank"></i></span>
            </div>
        </div>
    </section>
</footer>

@push('styles')
<style>
    footer .badge.bg-secondary-subtle { background: rgba(255,255,255,.08) !important; border: 1px solid rgba(255,255,255,.09); }
</style>
@endpush

@push('scripts-page')
<script>
    (function() {
        // Back-to-top
        const btnTop = document.getElementById('btn-back-to-top');
        if (btnTop) {
            window.addEventListener('scroll', () => {
                btnTop.style.display = window.scrollY > 400 ? 'inline-flex' : 'none';
            });
            btnTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
        }

        // Newsletter AJAX (chỉ chạy nếu có route)
        const form = document.getElementById('newsletter-form');
        if (form && form.action && form.action !== window.location.origin + '#') {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const email = form.querySelector('input[name="email"]').value.trim();
                const err = document.getElementById('newsletter-error');
                const ok  = document.getElementById('newsletter-success');
                if (err) { err.classList.add('d-none'); err.textContent = ''; }
                if (ok)  { ok.classList.add('d-none'); }

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ email })
                    });
                    if (!res.ok) throw new Error('Request failed');
                    const data = await res.json().catch(() => ({}));
                    if (data.success) {
                        if (ok) ok.classList.remove('d-none');
                        form.reset();
                    } else {
                        if (err) { err.textContent = data.message || 'Đăng ký thất bại. Vui lòng thử lại.'; err.classList.remove('d-none'); }
                    }
                } catch (ex) {
                    if (err) { err.textContent = 'Không thể gửi yêu cầu lúc này. Vui lòng thử lại sau.'; err.classList.remove('d-none'); }
                    console.error(ex);
                }
            }, { passive: false });
        }
    })();
</script>
@endpush
