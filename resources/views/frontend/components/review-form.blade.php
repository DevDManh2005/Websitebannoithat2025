<div class="mt-4 review-form" data-aos="fade-up">
    <h5 class="mb-3">Gửi đánh giá của bạn</h5>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('reviews.store', $product->id) }}" enctype="multipart/form-data">
        @csrf

        {{-- Chọn số sao --}}
        <div class="mb-3">
            <label for="rating" class="form-label">Đánh giá:</label>
            <select name="rating" id="rating" class="form-select" required>
                <option value="">Chọn sao</option>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}">{{ $i }} sao</option>
                @endfor
            </select>
        </div>

        {{-- Nội dung đánh giá --}}
        <div class="mb-3">
            <label for="review" class="form-label">Nội dung:</label>
            <textarea name="review" id="review" rows="4" class="form-control" required>{{ old('review') }}</textarea>
        </div>

        {{-- Hình ảnh --}}
        <div class="mb-3">
            <label for="image" class="form-label">Hình ảnh (tuỳ chọn):</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-1"></i> Gửi đánh giá
        </button>
    </form>
</div>
<style>
    /* === FORM ĐÁNH GIÁ === */
.review-form {
    background-color: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease-in-out;
}

.review-form:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.review-form h5 {
    font-weight: 600;
    color: #A20E38;
    margin-bottom: 1.5rem;
}

.review-form .form-label {
    font-weight: 500;
    margin-bottom: 6px;
    color: #333;
}

.review-form select,
.review-form textarea,
.review-form input[type="file"] {
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 15px;
    border: 1px solid #ced4da;
    transition: border-color 0.2s ease;
}

.review-form select:focus,
.review-form textarea:focus,
.review-form input[type="file"]:focus {
    border-color: #A20E38;
    box-shadow: 0 0 0 0.15rem rgba(162, 14, 56, 0.25);
}

.review-form button {
    background-color: #A20E38;
    border: none;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.review-form button:hover {
    background-color: #8d0c30;
}

.review-form .alert {
    border-radius: 8px;
    font-size: 14px;
    padding: 10px 16px;
}

.review-form .alert ul {
    margin-bottom: 0;
}

</style>