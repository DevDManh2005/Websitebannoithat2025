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
        
        {{-- Main Offer - Nâng cấp quan trọng nhất --}}
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
        :root {
            /* Đảm bảo bạn đã có các biến màu này */
            --brand: #A20E38;
            --brand-rgb: 162, 14, 56;
            --text: #2B2623;
            --muted: #7D726C;
            --card: #FFFFFF;
            --bg: #FFF6F8;
            --radius: 12px;
            --shadow: 0 8px 24px rgba(32, 25, 21, .07);
        }

        .voucher-card-v3 {
            display: flex;
            position: relative;
            background: var(--card, #fff);
            border-radius: var(--radius, 12px);
            box-shadow: var(--shadow);
            min-height: 110px;
            max-width: 420px;
            margin: 0.75rem auto;
            transition: transform .2s ease, box-shadow .2s ease;
            overflow: hidden; /* Important for rip effect */
        }
        .voucher-card-v3:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 14px 32px rgba(32, 25, 21, .1);
        }

        .voucher-left-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px; /* Slimmer */
            background-color: var(--brand);
            color: #fff;
            font-size: 2.2rem;
            flex-shrink: 0;
            border-right: 2px dotted rgba(255,255,255,0.5); /* Replaces separator */
        }
        
        /* New Rip Separator - simpler and cleaner */
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
            background: var(--bg, #FFF6F8); /* Match page background */
            border-radius: 50%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }
        .voucher-rip-separator::before { top: -10px; }
        .voucher-rip-separator::after { bottom: -10px; }

        .voucher-right-panel {
            display: flex;
            flex-direction: column; /* Changed to column */
            justify-content: space-between; /* Space out items */
            flex-grow: 1;
            padding: 0.75rem 1rem 0.75rem 1.25rem;
            gap: 0.5rem; /* Add gap between elements */
        }

        /* NEW: Main Offer Styling */
        .voucher-main-offer {
            line-height: 1.2;
        }
        .voucher-value {
            font-size: 1.4rem; /* Larger font */
            font-weight: 700;
            color: var(--brand);
            margin: 0;
        }
        .voucher-condition {
            font-size: 0.85rem;
            color: var(--muted);
            margin: 0;
        }

        /* NEW: Code and Actions Styling */
        .voucher-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .voucher-code-wrapper {
            /* No changes needed */
        }
        .voucher-code {
            font-family: 'Courier New', Courier, monospace;
            background: rgba(var(--brand-rgb), 0.1);
            color: var(--brand);
            padding: .2em .5em;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 700;
            border: 1px dashed rgba(var(--brand-rgb), 0.3);
        }
        
        .btn-icon-info {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: transparent;
            border: 0;
            border-radius: 50%;
            width: 28px; height: 28px;
            color: var(--muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background .2s, color .2s;
        }
        .btn-icon-info:hover {
            background: rgba(0,0,0,0.05);
            color: var(--text);
        }
        
        /* SweetAlert Customization (no changes needed) */
        .swal2-popup .swal2-html-container ul {
            padding-left: 1rem; margin-top: 1rem; text-align: left;
        }
        .swal2-popup .swal2-html-container li {
            margin-bottom: 0.5rem;
        }
    </style>
    @endpush

    @push('scripts-page')
    {{-- PHẦN SCRIPT GIỮ NGUYÊN - ĐÃ RẤT TỐT --}}
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