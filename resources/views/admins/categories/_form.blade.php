@php
    use Illuminate\Support\Str;
    $img = $category->image ?? null;
    $imgUrl = $img
        ? (Str::startsWith($img, ['http://','https://','//']) ? $img : asset('storage/'.$img))
        : null;

    // Hàm đệ quy để hiển thị danh mục dạng cây
    function renderCategoryOptions($categories, $exceptIds = [], $level = 0, $prefix = '') {
        $output = '';
        foreach ($categories as $cat) {
            if (in_array($cat->id, $exceptIds)) {
                continue; // Bỏ qua danh mục hiện tại và danh mục con
            }
            $indent = str_repeat('—', $level) . ($level > 0 ? ' ' : '');
            $selected = (string)old('parent_id', isset($category) ? $category->parent_id : '') === (string)$cat->id ? 'selected' : '';
            $output .= '<option value="' . $cat->id . '" ' . $selected . '>' . $prefix . $indent . $cat->name . '</option>';
            if ($cat->children->isNotEmpty()) {
                $output .= renderCategoryOptions($cat->children, $exceptIds, $level + 1, $prefix);
            }
        }
        return $output;
    }

    // Lấy danh sách các ID cần loại trừ (danh mục hiện tại và danh mục con)
    $exceptIds = [];
    if (isset($category) && $category->id) {
        $exceptIds[] = $category->id; // Loại trừ danh mục hiện tại
        $collectChildIds = function($cat) use (&$collectChildIds, &$exceptIds) {
            foreach ($cat->children as $child) {
                $exceptIds[] = $child->id;
                if ($child->children->isNotEmpty()) {
                    $collectChildIds($child);
                }
            }
        };
        $collectChildIds($category);
    }
@endphp

@push('styles')
<style>
    /* Đồng bộ với bảng màu và style từ app.blade.php */
    .category-form {
        background: linear-gradient(180deg, rgba(234,223,206,.25), transparent), var(--card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 1.5rem;
    }

    .category-form .form-label {
        color: var(--text);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .category-form .form-control,
    .category-form .form-select {
        border-radius: 10px;
        border: 1px solid rgba(196,111,59,.25);
        background: #fff6ed;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .category-form .form-control:focus,
    .category-form .form-select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px var(--ring);
        transform: translateY(-1px);
    }

    .category-form .form-control.is-invalid,
    .category-form .form-select.is-invalid {
        border-color: #dc3545;
        background: #fde7e7;
    }

    .category-form .form-select option {
        padding-left: calc(10px * var(--level, 0));
    }

    .category-form .form-check-input {
        border-radius: 4px;
        border: 1px solid rgba(196,111,59,.25);
        cursor: pointer;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .category-form .form-check-input:checked {
        background-color: var(--brand);
        border-color: var(--brand);
    }

    .category-form .form-check-input:focus {
        box-shadow: 0 0 0 3px var(--ring);
    }

    .category-form .form-check-input.is-invalid {
        border-color: #dc3545;
    }

    .category-form .img-preview {
        max-height: 200px;
        object-fit: cover;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .category-form .img-preview.show {
        opacity: 1;
    }

    .category-form .form-control[type="file"] {
        padding: 0.5rem;
    }

    .category-form .form-check-label {
        color: var(--text);
        cursor: pointer;
    }

    .category-form .ripple {
        position: relative;
        overflow: hidden;
    }

    .category-form .ripple:after {
        content: '';
        position: absolute;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(196,111,59,.35);
        transform: translate(-50%,-50%);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.6s, width 0.6s, height 0.6s;
    }

    .category-form .ripple:active:after {
        width: 100px;
        height: 100px;
        opacity: 0.6;
    }

    /* Responsive */
    @media (max-width: 767.98px) {
        .category-form {
            padding: 1rem;
            border-radius: 12px;
        }

        .category-form .form-label {
            font-size: 0.9rem;
        }

        .category-form .form-control,
        .category-form .form-select {
            font-size: 0.9rem;
        }
    }
</style>
@endpush

<div class="category-form">
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="mb-3">
                <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $category->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="parent_id" class="form-label">Danh mục cha</label>
                <select name="parent_id" id="parent_id"
                        class="form-select @error('parent_id') is-invalid @enderror">
                    <option value="">— Là danh mục gốc —</option>
                    {!! renderCategoryOptions($parents, $exceptIds, 0, '') !!}
                </select>
                @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="position" class="form-label">Vị trí</label>
                <input type="number" name="position" id="position"
                       class="form-control @error('position') is-invalid @enderror"
                       value="{{ old('position', $category->position ?? 0) }}" min="0">
                @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-lg-4">
            <div class="mb-3">
                <label class="form-label">Hình ảnh</label>
                <input type="file" name="image" id="image"
                       class="form-control @error('image') is-invalid @enderror"
                       accept="image/*">
                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

                <div class="mt-2">
                    @if($imgUrl)
                        <img id="catPreview" src="{{ $imgUrl }}" class="img-preview img-fluid rounded border show" alt="preview">
                    @else
                        <img id="catPreview" class="img-preview img-fluid rounded border" alt="preview">
                    @endif
                </div>
            </div>

            <div class="form-check form-switch mb-3">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input ripple @error('is_active') is-invalid @enderror"
                       type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Hiển thị</label>
                @error('is_active') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Preview ảnh với hiệu ứng fade-in
    document.getElementById('image')?.addEventListener('change', function(e) {
        const f = e.target.files?.[0];
        const img = document.getElementById('catPreview');
        if (!img) return;
        if (!f) {
            img.src = '';
            img.classList.remove('show');
            return;
        }
        const r = new FileReader();
        r.onload = ev => {
            img.src = ev.target.result;
            img.classList.add('show');
        };
        r.readAsDataURL(f);
    });

    // Hiệu ứng ripple cho checkbox
    document.querySelectorAll('.form-check-input.ripple').forEach(el => {
        el.addEventListener('click', function(e) {
            const btn = e.currentTarget;
            const rect = btn.getBoundingClientRect();
            const circle = document.createElement('span');
            const d = Math.max(rect.width, rect.height);
            Object.assign(circle.style, {
                width: d + 'px',
                height: d + 'px',
                position: 'absolute',
                left: (e.clientX - rect.left) + 'px',
                top: (e.clientY - rect.top) + 'px',
                transform: 'translate(-50%,-50%)',
                background: 'rgba(196,111,59,.35)',
                borderRadius: '50%',
                pointerEvents: 'none',
                opacity: '0.6',
                transition: 'opacity .6s, width .6s, height .6s'
            });
            btn.appendChild(circle);
            requestAnimationFrame(() => {
                circle.style.width = circle.style.height = (d * 1.8) + 'px';
                circle.style.opacity = '0';
            });
            setTimeout(() => circle.remove(), 600);
        });
    });
</script>
@endpush