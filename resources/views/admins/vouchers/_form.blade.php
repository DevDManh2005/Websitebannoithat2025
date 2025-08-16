<div class="row">
    <div class="col-md-6 mb-3">
        <label for="code" class="form-label">Mã Voucher <span class="text-danger">*</span></label>
        <input
            type="text"
            class="form-control @error('code') is-invalid @enderror"
            id="code" name="code"
            value="{{ old('code', $voucher->code ?? '') }}"
            required
        >
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="type" class="form-label">Loại <span class="text-danger">*</span></label>
        <select
            name="type" id="type"
            class="form-select @error('type') is-invalid @enderror"
            required
        >
            <option value="fixed"   @selected(old('type', $voucher->type ?? '') == 'fixed')>Giảm giá cố định</option>
            <option value="percent" @selected(old('type', $voucher->type ?? '') == 'percent')>Giảm theo phần trăm</option>
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="value" class="form-label">Giá trị <span class="text-danger">*</span></label>
    <input
        type="number"
        class="form-control @error('value') is-invalid @enderror"
        id="value" name="value"
        value="{{ old('value', $voucher->value ?? '') }}"
        required
        min="0" step="0.01"
    >
    <small id="valueHelp" class="form-text text-muted">
        Nếu là %, nhập giá trị từ 0 đến 50 (ví dụ: 10 = giảm 10%). Nếu là giá cố định, nhập số tiền (ví dụ: 50000).
    </small>
    @error('value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="min_order_amount" class="form-label">Giá trị đơn hàng tối thiểu</label>
    <input
        type="number"
        class="form-control @error('min_order_amount') is-invalid @enderror"
        id="min_order_amount" name="min_order_amount"
        value="{{ old('min_order_amount', $voucher->min_order_amount ?? 0) }}"
        min="0" step="0.01"
    >
    @error('min_order_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="usage_limit" class="form-label">Giới hạn lượt sử dụng</label>
    <input
        type="number"
        class="form-control @error('usage_limit') is-invalid @enderror"
        id="usage_limit" name="usage_limit"
        value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}"
        min="1" step="1"
    >
    @error('usage_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="start_at" class="form-label">Ngày bắt đầu</label>
        <input
            type="datetime-local"
            class="form-control @error('start_at') is-invalid @enderror"
            id="start_at" name="start_at"
            value="{{ old('start_at', optional($voucher->start_at ?? null)->format('Y-m-d\TH:i')) }}"
        >
        @error('start_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="end_at" class="form-label">Ngày kết thúc</label>
        <input
            type="datetime-local"
            class="form-control @error('end_at') is-invalid @enderror"
            id="end_at" name="end_at"
            value="{{ old('end_at', optional($voucher->end_at ?? null)->format('Y-m-d\TH:i')) }}"
        >
        @error('end_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input
        class="form-check-input"
        type="checkbox" id="is_active"
        name="is_active" value="1"
        {{ old('is_active', $voucher->is_active ?? true) ? 'checked' : '' }}
    >
    <label class="form-check-label" for="is_active">Kích hoạt</label>
</div>

@push('scripts-page')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeEl = document.getElementById('type');
    const valueEl = document.getElementById('value');
    const helpEl = document.getElementById('valueHelp');
    const MAX_PERCENT = 50;

    function showAlert(message) {
        if (window.Swal) {
            Swal.fire({
                toast: true,
                icon: 'warning',
                title: message,
                timer: 1800,
                showConfirmButton: false,
                position: 'top-end'
            });
        } else {
            alert(message); // Fallback nếu SweetAlert2 không khả dụng
        }
    }

    function syncHelpAndConstraints() {
        const isPercent = typeEl.value === 'percent';
        helpEl.textContent = isPercent
            ? 'Nhập giá trị từ 0 đến 50 (ví dụ: 10 = giảm 10%).'
            : 'Nhập số tiền giảm cố định (đơn vị VNĐ). Ví dụ: 50000.';
        valueEl.setAttribute('step', isPercent ? '1' : '1000');
        valueEl.setAttribute('min', '0');
        if (isPercent) {
            valueEl.setAttribute('max', MAX_PERCENT);
            const val = parseFloat(valueEl.value || '0');
            if (val > MAX_PERCENT) {
                valueEl.value = MAX_PERCENT;
                showAlert('Mức giảm theo % không được vượt quá 50%');
            }
        } else {
            valueEl.removeAttribute('max');
        }
    }

    ['keyup', 'change', 'blur'].forEach(evt => {
        valueEl.addEventListener(evt, () => {
            if (typeEl.value === 'percent') {
                const val = parseFloat(valueEl.value || '0');
                if (val > MAX_PERCENT) {
                    valueEl.value = MAX_PERCENT;
                    showAlert('Mức giảm theo % không được vượt quá 50%');
                }
            }
        });
    });

    typeEl.addEventListener('change', syncHelpAndConstraints);
    syncHelpAndConstraints();

    // Toast kết quả submit
    @if (session('success'))
    if (window.Swal) {
        Swal.fire({
            toast: true,
            icon: 'success',
            title: @json(session('success')),
            timer: 2000,
            showConfirmButton: false,
            position: 'top-end'
        });
    }
    @endif

    // Nếu có lỗi validate
    @if ($errors->any())
    if (window.Swal) {
        Swal.fire({
            icon: 'error',
            title: 'Dữ liệu chưa hợp lệ',
            html: `{!! collect($errors->all())->map(fn($e)=>'<div class="text-start">• '.e($e).'</div>')->implode('') !!}`,
        });
    }
    @endif
});
</script>
@endpush