@props(['voucher'])

<div class="voucher-card-v3">
    {{-- Left Panel (Icon) --}}
    <div class="voucher-left-panel">
        @switch($voucher->type)
            @case('percent')
                <i class="bi bi-percent"></i>
                @break
            @case('cash')
                <i class="bi bi-cash-coin"></i>
                @break
            @default
                <i class="bi bi-ticket-detailed"></i>
        @endswitch
    </div>

    {{-- Separator with Rip Effect --}}
    <div class="voucher-rip-separator"></div>

    {{-- Right Panel (Content) --}}
    <div class="voucher-right-panel">
        {{-- Main Offer --}}
        <div class="voucher-main-offer">
            <h5 class="voucher-value">
                GIẢM 
                @if($voucher->type == 'percent')
                    {{ $voucher->value }}%
                @else
                    {{ number_format($voucher->value, 0, ',', '.') }}đ
                @endif
            </h5>
            @if($voucher->min_order_amount > 0)
                <p class="voucher-condition">
                    Cho đơn từ {{ number_format($voucher->min_order_amount, 0, ',', '.') }}đ
                </p>
            @endif
        </div>

        {{-- Code and Actions --}}
        <div class="voucher-actions">
            <div class="voucher-code-wrapper">
                <kbd class="voucher-code">{{ $voucher->code }}</kbd>
            </div>
            <button class="btn btn-brand btn-sm px-3 copy-voucher-btn" data-voucher-code="{{ $voucher->code }}">
                Sao chép
            </button>
        </div>
    </div>

    {{-- Info Button --}}
    <button class="btn-icon-info" title="Xem chi tiết" onclick="showVoucherInfo({{ $voucher->toJson() }})">
        <i class="bi bi-info-circle"></i>
    </button>
</div>

@once
    @push('styles')
    <style>
        /* =================== Voucher Card =================== */
        .voucher-card-v3 {
            display: flex;
            position: relative;
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(15, 23, 42, 0.04);
            min-height: 110px;
            max-width: 420px;
            margin: 0.75rem auto;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            overflow: hidden;
        }
        .voucher-card-v3:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        /* =================== Left Panel =================== */
        .voucher-left-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            background-color: var(--brand);
            color: #fff;
            font-size: 2.2rem;
            flex-shrink: 0;
            border-right: 2px dotted rgba(255, 255, 255, 0.5);
            transition: background 0.2s ease;
        }

        /* =================== Rip Separator =================== */
        .voucher-rip-separator {
            position: absolute;
            left: 70px;
            top: 0;
            bottom: 0;
            width: 0;
        }
        .voucher-rip-separator::before,
        .voucher-rip-separator::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: var(--bg);
            border-radius: 50%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }
        .voucher-rip-separator::before { top: -10px; }
        .voucher-rip-separator::after { bottom: -10px; }

        /* =================== Right Panel =================== */
        .voucher-right-panel {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-grow: 1;
            padding: 0.75rem 1rem 0.75rem 1.25rem;
            gap: 0.5rem;
        }

        /* =================== Main Offer =================== */
        .voucher-main-offer {
            line-height: 1.2;
        }
        .voucher-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--brand);
            margin: 0;
        }
        .voucher-condition {
            font-size: 0.85rem;
            color: var(--muted);
            margin: 0;
        }

        /* =================== Code and Actions =================== */
        .voucher-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .voucher-code {
            font-family: 'Courier New', Courier, monospace;
            background: rgba(var(--brand-rgb), 0.1);
            color: var(--brand);
            padding: 0.2em 0.5em;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 700;
            border: 1px dashed rgba(var(--brand-rgb), 0.3);
            transition: background 0.2s ease, color 0.2s ease;
        }

        /* =================== Buttons =================== */
        .btn-brand {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
            padding: 0.5rem 1rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .btn-brand:hover {
            background-color: var(--brand-600);
            border-color: var(--brand-600);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .btn-icon-info {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: transparent;
            border: 0;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            color: var(--muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease, color 0.2s ease;
        }
        .btn-icon-info:hover {
            background: rgba(var(--brand-rgb), 0.1);
            color: var(--brand);
        }

        /* =================== SweetAlert Customization =================== */
        .swal2-popup .swal2-html-container ul {
            padding-left: 1rem;
            margin-top: 1rem;
            text-align: left;
        }
        .swal2-popup .swal2-html-container li {
            margin-bottom: 0.5rem;
        }

        /* =================== Responsive Design =================== */
        @media (max-width: 991px) {
            .voucher-card-v3 {
                min-height: 100px;
                max-width: 100%;
            }
            .voucher-left-panel {
                width: 60px;
                font-size: 2rem;
            }
            .voucher-rip-separator {
                left: 60px;
            }
            .voucher-value {
                font-size: 1.3rem;
            }
            .voucher-condition {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 767px) {
            .voucher-card-v3 {
                min-height: 90px;
            }
            .voucher-left-panel {
                width: 50px;
                font-size: 1.8rem;
            }
            .voucher-rip-separator {
                left: 50px;
            }
            .voucher-right-panel {
                padding: 0.5rem 0.75rem 0.5rem 1rem;
            }
            .voucher-value {
                font-size: 1.2rem;
            }
            .voucher-condition {
                font-size: 0.75rem;
            }
            .voucher-code {
                font-size: 0.9rem;
                padding: 0.15em 0.4em;
            }
            .btn-sm {
                padding: 0.35rem 0.7rem;
                font-size: 0.85rem;
            }
            .btn-icon-info {
                width: 24px;
                height: 24px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 575px) {
            .voucher-card-v3 {
                min-height: 80px;
            }
            .voucher-left-panel {
                width: 40px;
                font-size: 1.6rem;
            }
            .voucher-rip-separator {
                left: 40px;
            }
            .voucher-right-panel {
                padding: 0.5rem 0.5rem 0.5rem 0.75rem;
            }
            .voucher-value {
                font-size: 1.1rem;
            }
            .voucher-condition {
                font-size: 0.7rem;
            }
            .voucher-code {
                font-size: 0.85rem;
                padding: 0.1em 0.3em;
            }
            .btn-sm {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }
            .btn-icon-info {
                width: 22px;
                height: 22px;
                font-size: 0.85rem;
            }
            .swal2-popup .swal2-html-container ul {
                font-size: 0.85rem;
            }
        }
    </style>
    @endpush

    @push('scripts-page')
    <script>
        if (!window.copyVoucherListenerAdded) {
            document.body.addEventListener('click', function (event) {
                const copyBtn = event.target.closest('.copy-voucher-btn');
                if (!copyBtn) return;
                const codeToCopy = copyBtn.dataset.voucherCode;
                const originalText = copyBtn.innerHTML;
                navigator.clipboard.writeText(codeToCopy).then(() => {
                    copyBtn.innerHTML = 'Đã chép!';
                    copyBtn.classList.remove('btn-brand');
                    copyBtn.classList.add('btn-success');
                    copyBtn.disabled = true;
                    setTimeout(() => {
                        copyBtn.innerHTML = originalText;
                        copyBtn.classList.remove('btn-success');
                        copyBtn.classList.add('btn-brand');
                        copyBtn.disabled = false;
                    }, 2000);
                }).catch(err => {
                    console.error('Không thể sao chép mã: ', err);
                });
            });
            window.copyVoucherListenerAdded = true;
        }
        if (typeof showVoucherInfo !== 'function') {
            function showVoucherInfo(voucher) {
                const formatDate = (dateString) => {
                    if (!dateString) return 'Không giới hạn';
                    return new Intl.DateTimeFormat('vi-VN').format(new Date(dateString));
                };
                const formatMoney = (amount) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
                const html = `
                    <ul class="list-unstyled text-start m-0 ps-3">
                        <li><strong>Mã:</strong> <kbd>${voucher.code}</kbd></li>
                        <li><strong>Loại:</strong> ${voucher.type === 'percent' ? 'Giảm %' : 'Giảm tiền'}</li>
                        <li><strong>Giá trị:</strong> ${voucher.type === 'percent' ? voucher.value + '%' : formatMoney(voucher.value)}</li>
                        <li><strong>Đơn tối thiểu:</strong> ${formatMoney(voucher.min_order_amount)}</li>
                        <li><strong>Thời gian áp dụng:</strong> ${formatDate(voucher.start_at)} – ${formatDate(voucher.end_at)}</li>
                        <li><strong>Lượt sử dụng:</strong> ${voucher.used_count}/${voucher.usage_limit ?? 'Không giới hạn'}</li>
                    </ul>
                `;
                Swal.fire({
                    icon: 'info',
                    title: `Thông tin mã giảm giá`,
                    html: html,
                    confirmButtonText: 'Đóng'
                });
            }
        }
    </script>
    @endpush
@endonce