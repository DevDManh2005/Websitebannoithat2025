@php($voucher = $voucher ?? null)

@push('styles')
<style>
  #voucher-form .hint{ color:#6c757d }
  #voucher-form .chip{
    display:inline-block; padding:.22rem .6rem; border-radius:999px;
    background:#f2f4f7; border:1px solid rgba(23,26,31,.12); font-weight:600; font-size:.82rem;
  }
</style>
@endpush

<div id="voucher-form">
  <div class="row">
    <div class="col-md-6 mb-3">
      <label for="code" class="form-label">Mã Voucher <span class="text-danger">*</span></label>
      <div class="input-group">
        <input
          type="text"
          class="form-control @error('code') is-invalid @enderror"
          id="code" name="code"
          value="{{ old('code', $voucher->code ?? '') }}"
          placeholder="VD: SPRING10" required
        >
        <button class="btn btn-outline-secondary" type="button" id="btnGenCode" title="Tạo ngẫu nhiên">
          <i class="bi bi-magic"></i>
        </button>
        @error('code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
      </div>
      <div class="small hint mt-1">Dùng chữ in hoa, số, gạch nối. Ví dụ: <span class="chip">SALE-2025</span></div>
    </div>

    <div class="col-md-6 mb-3">
      <label for="type" class="form-label">Loại <span class="text-danger">*</span></label>
      <select
        name="type" id="type"
        class="form-select @error('type') is-invalid @enderror" required
      >
        <option value="fixed"   @selected(old('type', $voucher->type ?? '') == 'fixed')>Giảm giá cố định</option>
        <option value="percent" @selected(old('type', $voucher->type ?? '') == 'percent')>Giảm theo phần trăm</option>
      </select>
      @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="mb-3">
    <label for="value" class="form-label">Giá trị <span class="text-danger">*</span></label>
    <div class="input-group">
      <input
        type="number"
        class="form-control @error('value') is-invalid @enderror"
        id="value" name="value"
        value="{{ old('value', $voucher->value ?? '') }}"
        required min="0" step="0.01" inputmode="decimal"
      >
      <span class="input-group-text" id="valueUnit">₫</span>
    </div>
    <small id="valueHelp" class="form-text hint">
      Nếu là %, nhập 0–50 (ví dụ: 10 = giảm 10%). Nếu là giá cố định, nhập số tiền (ví dụ: 50000).
    </small>
    @error('value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
  </div>

  <div class="mb-3">
    <label for="min_order_amount" class="form-label">Giá trị đơn hàng tối thiểu</label>
    <div class="input-group">
      <input
        type="number"
        class="form-control @error('min_order_amount') is-invalid @enderror"
        id="min_order_amount" name="min_order_amount"
        value="{{ old('min_order_amount', $voucher->min_order_amount ?? 0) }}"
        min="0" step="1000" inputmode="decimal"
      >
      <span class="input-group-text">₫</span>
    </div>
    @error('min_order_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="mb-3">
    <label for="usage_limit" class="form-label">Giới hạn lượt sử dụng</label>
    <input
      type="number"
      class="form-control @error('usage_limit') is-invalid @enderror"
      id="usage_limit" name="usage_limit"
      value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}"
      min="1" step="1" placeholder="Để trống nếu không giới hạn"
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
        value="{{ old('start_at', optional($voucher->start_at ?? null)?->format('Y-m-d\TH:i')) }}"
      >
      @error('start_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
      <label for="end_at" class="form-label">Ngày kết thúc</label>
      <input
        type="datetime-local"
        class="form-control @error('end_at') is-invalid @enderror"
        id="end_at" name="end_at"
        value="{{ old('end_at', optional($voucher->end_at ?? null)?->format('Y-m-d\TH:i')) }}"
      >
      @error('end_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="form-check form-switch mb-1">
    <input type="hidden" name="is_active" value="0">
    <input
      class="form-check-input" type="checkbox" id="is_active"
      name="is_active" value="1"
      {{ old('is_active', $voucher->is_active ?? true) ? 'checked' : '' }}
    >
    <label class="form-check-label" for="is_active">Kích hoạt voucher</label>
  </div>
  <div class="hint small">Voucher chỉ hoạt động khi trong khoảng thời gian hợp lệ (nếu có thiết lập).</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeEl = document.getElementById('type');
    const valueEl = document.getElementById('value');
    const helpEl = document.getElementById('valueHelp');
    const unitEl = document.getElementById('valueUnit');
    const btnGen = document.getElementById('btnGenCode');
    const codeEl = document.getElementById('code');
    const MAX_PERCENT = 50;

    // MỚI: Lấy thêm element của ô "Giá trị đơn hàng tối thiểu"
    const minOrderAmountEl = document.getElementById('min_order_amount');

    function showToast(message, icon = 'warning') {
        if (window.Swal) {
            Swal.fire({ toast: true, icon, title: message, timer: 2500, showConfirmButton: false, position: 'top-end' });
        } else { alert(message); }
    }

    // MỚI: Hàm kiểm tra logic "tối thiểu > giá trị giảm"
    function validateMinOrderAmount() {
        // Chỉ áp dụng khi là loại "Giảm giá cố định"
        if (typeEl.value !== 'fixed') {
            return;
        }

        const voucherValue = parseFloat(valueEl.value || '0');
        const minOrderValue = parseFloat(minOrderAmountEl.value || '0');

        // Nếu có đặt giá trị tối thiểu và nó không lớn hơn mức giảm giá -> cảnh báo
        if (minOrderValue > 0 && minOrderValue <= voucherValue) {
            showToast('Giá trị đơn hàng tối thiểu phải lớn hơn mức giảm giá.');
            // Bạn có thể thêm logic tự động sửa, ví dụ: minOrderAmountEl.value = '';
        }
    }

    function syncValueUI() {
        const isPercent = typeEl.value === 'percent';
        unitEl.textContent = isPercent ? '%' : '₫';
        helpEl.textContent = isPercent
            ? 'Nhập giá trị từ 0 đến 50 (ví dụ: 10 = giảm 10%).'
            : 'Nhập số tiền giảm cố định (đơn vị VNĐ). Ví dụ: 50000.';
        valueEl.setAttribute('min', '0');
        valueEl.setAttribute('step', isPercent ? '1' : '1000');
        if (isPercent) {
            valueEl.setAttribute('max', String(MAX_PERCENT));
            const v = parseFloat(valueEl.value || '0');
            if (v > MAX_PERCENT) { valueEl.value = MAX_PERCENT; showToast('Mức giảm theo % không được vượt quá 50%'); }
        } else {
            valueEl.removeAttribute('max');
        }
        // MỚI: Gọi hàm validate mỗi khi UI thay đổi
        validateMinOrderAmount();
    }

    ['change', 'keyup', 'blur'].forEach(evt => {
        valueEl.addEventListener(evt, () => {
            if (typeEl.value === 'percent') {
                let v = parseFloat(valueEl.value || '0');
                if (v > MAX_PERCENT) { valueEl.value = MAX_PERCENT; showToast('Mức giảm theo % không được vượt quá 50%'); }
                if (v < 0) { valueEl.value = 0; }
            } else {
                let v = parseFloat(valueEl.value || '0');
                if (v < 0) { valueEl.value = 0; }
            }
            // MỚI: Gọi hàm validate khi giá trị voucher thay đổi
            validateMinOrderAmount();
        });
    });

    // MỚI: Thêm listener cho ô giá trị tối thiểu
    minOrderAmountEl.addEventListener('change', validateMinOrderAmount);
    minOrderAmountEl.addEventListener('keyup', validateMinOrderAmount);

    typeEl.addEventListener('change', syncValueUI);
    syncValueUI();

    btnGen?.addEventListener('click', () => {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let res = [];
        for (let i = 0; i < 8; i++) { res.push(chars[Math.floor(Math.random() * chars.length)]); }
        const code = res.join('');
        codeEl.value = code;
        showToast('Đã tạo mã: ' + code, 'success');
    });

    const startEl = document.getElementById('start_at');
    const endEl = document.getElementById('end_at');
    [startEl, endEl].forEach(el => el?.addEventListener('change', () => {
        if (startEl?.value && endEl?.value) {
            const s = new Date(startEl.value);
            const e = new Date(endEl.value);
            if (e < s) {
                endEl.value = startEl.value;
                showToast('Ngày kết thúc phải sau ngày bắt đầu');
            }
        }
    }));

    @if (session('success'))
        if (window.Swal) Swal.fire({ toast: true, icon: 'success', title: @json(session('success')), timer: 2000, showConfirmButton: false, position: 'top-end' });
    @endif
    @if ($errors->any())
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Dữ liệu chưa hợp lệ', html: `{!! collect($errors->all())->map(fn($e)=>'<div class="text-start">• '.e($e).'</div>')->implode('') !!}` });
    @endif
});
</script>
@endpush