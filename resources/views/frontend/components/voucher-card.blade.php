@props(['voucher'])

@php
    $iconUrl = match ($voucher->type) {
        'percent' => 'https://cdn-icons-png.flaticon.com/512/1170/1170576.png',
        'cash' => 'https://cdn-icons-png.flaticon.com/512/942/942751.png',
        default => 'https://cdn-icons-png.flaticon.com/512/869/869649.png'
    };
@endphp

<div class="voucher-card d-flex align-items-stretch position-relative">
    {{-- Icon --}}
    <div class="voucher-icon d-flex align-items-center justify-content-center p-2">
        <img src="{{ $iconUrl }}" alt="Voucher Icon" class="img-fluid" style="width: 36px; height: 36px;">
    </div>

    {{-- Răng cưa --}}
    <div class="voucher-separator"></div>

    {{-- Nội dung --}}
    <div class="voucher-body d-flex justify-content-between align-items-center flex-grow-1 px-3 py-3">
        <div class="info">
            <div class="small text-muted">Mã:</div>
            <div class="fw-semibold text-dark">{{ $voucher->code }}</div>
            <div class="small text-muted mt-1">HSD: {{ optional($voucher->end_at)->format('d/m/Y') }}</div>
        </div>

        <div class="actions text-end">
            {{-- THAY ĐỔI QUAN TRỌNG: Bỏ onclick, thêm class và data-attribute --}}
            <button class="btn btn-danger btn-sm px-4 copy-voucher-btn" data-voucher-code="{{ $voucher->code }}">
                Sao chép mã
            </button>
        </div>
    </div>

    {{-- Nút xem chi tiết --}}
    <div class="position-absolute top-0 end-0 mt-2 me-2 text-muted" title="Thông tin mã">
        <i class="bi bi-info-circle cursor-pointer fs-6" onclick="showVoucherInfo({{ $voucher->toJson() }})"></i>
    </div>
</div>

@once
    {{-- Đẩy CSS lên layout chính một lần duy nhất --}}
    @push('styles')
        <style>
            .voucher-card {
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid #f0e7d7;
                background: #fffaf3;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                min-height: 110px;
                width: 100%;
                max-width: 360px;
                transition: all 0.3s ease-in-out;
                margin: 0.5rem;
            }

            .voucher-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            }

            .voucher-icon {
                width: 50px;
                background: #fff0e0;
                display: flex;
            }

            .voucher-separator {
                width: 1px;
                background: repeating-linear-gradient(to bottom, #ccc, #ccc 6px, transparent 6px, transparent 12px);
            }

            .voucher-body .btn {
                font-weight: 500;
                border-radius: 6px;
            }

            .cursor-pointer {
                cursor: pointer;
            }

            .swal2-popup .swal2-html-container ul {
                padding-left: 1rem;
                margin-top: 1rem;
            }

            .swal2-popup .swal2-html-container li {
                margin-bottom: 0.5rem;
                font-size: 0.95rem;
                line-height: 1.5;
            }
        </style>
    @endpush

    {{-- Đẩy JavaScript lên layout chính một lần duy nhất --}}
    @push('scripts-page')
        <script>
            // --- LOGIC SAO CHÉP MÃ GIẢM GIÁ ---
            // Sử dụng event listener thay vì onclick để dễ dàng thao tác với button
            document.body.addEventListener('click', function (event) {
                const copyBtn = event.target.closest('.copy-voucher-btn');
                if (!copyBtn) return; // Nếu không phải nút sao chép thì bỏ qua

                const codeToCopy = copyBtn.dataset.voucherCode;
                const originalText = copyBtn.innerHTML;
                const originalClasses = [...copyBtn.classList]; // Lưu lại các class cũ

                navigator.clipboard.writeText(codeToCopy).then(() => {
                    // Thay đổi giao diện nút
                    copyBtn.innerHTML = 'Đã sao chép!';
                    copyBtn.classList.remove('btn-danger');
                    copyBtn.classList.add('btn-success');
                    copyBtn.disabled = true;

                    // Dùng setTimeout để trả lại trạng thái ban đầu sau 2 giây
                    setTimeout(() => {
                        copyBtn.innerHTML = originalText;
                        copyBtn.className = originalClasses.join(' '); // Khôi phục class cũ
                        copyBtn.disabled = false;
                    }, 2000);

                }).catch(err => {
                    console.error('Không thể sao chép mã: ', err);
                    alert('Sao chép thất bại!');
                });
            });

            // Hàm hiển thị thông tin chi tiết (giữ nguyên)
            function showVoucherInfo(voucher) {
                const formatDate = (dateString) => {
                    if (!dateString) return 'Không giới hạn';
                    return new Intl.DateTimeFormat('vi-VN').format(new Date(dateString));
                };
                const formatMoney = (amount) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);

                const html = `
                        <ul class="list-unstyled text-start m-0">
                            <li><strong>Loại:</strong> ${voucher.type === 'percent' ? 'Giảm %' : 'Giảm tiền'}</li>
                            <li><strong>Giá trị:</strong> ${voucher.type === 'percent' ? voucher.value + '%' : formatMoney(voucher.value)}</li>
                            <li><strong>Đơn tối thiểu:</strong> ${formatMoney(voucher.min_order_amount)}</li>
                            <li><strong>Thời gian áp dụng:</strong> ${formatDate(voucher.start_at)} – ${formatDate(voucher.end_at)}</li>
                            <li><strong>Lượt sử dụng:</strong> ${voucher.used_count}/${voucher.usage_limit ?? 'Không giới hạn'}</li>
                        </ul>
                    `;
                Swal.fire({
                    icon: 'info',
                    title: `Thông tin mã: ${voucher.code}`,
                    html: html,
                    confirmButtonText: 'Đóng'
                });
            }
        </script>
    @endpush
@endonce