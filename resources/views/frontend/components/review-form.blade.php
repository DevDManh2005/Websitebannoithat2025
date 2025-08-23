@props(['product'])

<div class="component-card card-glass review-form-wrapper" data-aos="fade-up">
    <h5 class="form-title">Gửi đánh giá của bạn</h5>

    @if(session('success'))
        <div class="alert alert-custom-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-custom-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-custom-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('reviews.store', $product->id) }}" enctype="multipart/form-data">
        @csrf

        {{-- Interactive Star Rating --}}
        <div class="mb-3">
            <label class="form-label">Đánh giá của bạn:</label>
            <div class="interactive-star-rating">
                <input type="radio" id="star5" name="rating" value="5" required><label for="star5" title="5 stars"></label>
                <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 stars"></label>
                <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 stars"></label>
                <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 stars"></label>
                <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 star"></label>
            </div>
        </div>

        {{-- Review Content --}}
        <div class="mb-3">
            <label for="review" class="form-label">Nội dung:</label>
            <textarea name="review" id="review" rows="4" class="form-control-custom" required placeholder="Sản phẩm rất tuyệt vời...">{{ old('review') }}</textarea>
        </div>

        {{-- Custom File Upload --}}
        <div class="mb-4">
            <label for="image" class="form-label">Hình ảnh (tuỳ chọn):</label>
            <div class="custom-file-upload">
                <input type="file" name="image" id="image" class="d-none" accept="image/*">
                <label for="image" class="btn btn-outline-brand btn-sm mb-0">
                    <i class="bi bi-upload me-2"></i> Chọn tệp...
                </label>
                <span class="file-name ms-3 text-muted">Chưa có tệp nào được chọn</span>
            </div>
        </div>

        <button type="submit" class="btn btn-brand w-100">
            <i class="bi bi-send me-2"></i> Gửi đánh giá
        </button>
    </form>
</div>

@once
@push('styles')
<style>
    .review-form-wrapper {
        padding: 1.5rem 2rem;
    }
    .form-title {
        font-weight: 700;
        color: var(--brand);
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text);
    }
    .form-control-custom {
        width: 100%;
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(0,0,0, .1);
        border-radius: 8px;
        padding: .6rem 1rem;
        transition: border-color .2s ease, box-shadow .2s ease;
    }
    .form-control-custom:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 0 4px var(--ring);
    }
    
    /* Interactive Star Rating */
    .interactive-star-rating {
        display: inline-flex;
        flex-direction: row-reverse;
        font-size: 2rem;
    }
    .interactive-star-rating input { display: none; }
    .interactive-star-rating label {
        color: #ddd;
        cursor: pointer;
        transition: color .2s;
    }
    .interactive-star-rating label::before { content: '★'; }
    .interactive-star-rating input:checked ~ label,
    .interactive-star-rating:hover label:hover ~ label {
        color: #ffc107;
    }
    .interactive-star-rating:hover label:hover {
        color: #ffc107 !important;
    }

    /* Custom File Upload */
    .file-name { font-size: 0.9rem; font-style: italic; }
    
    /* Themed Alerts */
    .alert-custom-success, .alert-custom-danger {
        border-radius: 8px;
        border: 1px solid transparent;
        padding: .8rem 1.2rem;
    }
    .alert-custom-success {
        background-color: rgba(25, 135, 84, 0.1);
        color: #0f5132;
        border-color: rgba(25, 135, 84, 0.2);
    }
    .alert-custom-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #842029;
        border-color: rgba(220, 53, 69, 0.2);
    }
</style>
@endpush

@push('scripts-page')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Custom File Input Logic
    const fileInput = document.getElementById('image');
    if (fileInput) {
        const fileNameSpan = fileInput.closest('.custom-file-upload').querySelector('.file-name');
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
            } else {
                fileNameSpan.textContent = 'Chưa có tệp nào được chọn';
            }
        });
    }
});
</script>
@endpush
@endonce