<div class="row">
    <div class="col-md-6 mb-3">
        <label for="code" class="form-label">Mã Voucher <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $voucher->code ?? '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="type" class="form-label">Loại <span class="text-danger">*</span></label>
        <select name="type" id="type" class="form-select" required>
            <option value="fixed" @selected(old('type', $voucher->type ?? '') == 'fixed')>Giảm giá cố định</option>
            <option value="percent" @selected(old('type', $voucher->type ?? '') == 'percent')>Giảm theo phần trăm</option>
        </select>
    </div>
</div>
<div class="mb-3">
    <label for="value" class="form-label">Giá trị <span class="text-danger">*</span></label>
    <input type="number" class="form-control" id="value" name="value" value="{{ old('value', $voucher->value ?? '') }}" required>
    <small class="form-text">Nếu là %, nhập 10 cho 10%. Nếu là giá cố định, nhập số tiền (ví dụ: 50000).</small>
</div>
<div class="mb-3">
    <label for="min_order_amount" class="form-label">Giá trị đơn hàng tối thiểu</label>
    <input type="number" class="form-control" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', $voucher->min_order_amount ?? 0) }}">
</div>
<div class="mb-3">
    <label for="usage_limit" class="form-label">Giới hạn lượt sử dụng</label>
    <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}">
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="start_at" class="form-label">Ngày bắt đầu</label>
        <input type="datetime-local" class="form-control" id="start_at" name="start_at" value="{{ old('start_at', optional($voucher->start_at ?? null)->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="end_at" class="form-label">Ngày kết thúc</label>
        <input type="datetime-local" class="form-control" id="end_at" name="end_at" value="{{ old('end_at', optional($voucher->end_at ?? null)->format('Y-m-d\TH:i')) }}">
    </div>
</div>
<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $voucher->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Kích hoạt</label>
</div>