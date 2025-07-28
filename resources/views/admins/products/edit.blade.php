@extends('admins.layouts.app')

@section('content')
<h1>Sửa sản phẩm: {{ $product->name }}</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Thông tin cơ bản --}}
    <div class="card mb-4">
        <div class="card-header">Thông tin cơ bản</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">Slug (URL thân thiện)</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="Để trống để tự động tạo">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                    <option value="">Chọn danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="brand_id" class="form-label">Thương hiệu</label>
                <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id">
                    <option value="">Không có</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
                @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="supplier_id" class="form-label">Nhà cung cấp</label>
                <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id">
                    <option value="">Không có</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả sản phẩm</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $product->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="label" class="form-label">Nhãn (ví dụ: Mới, Hot, Giảm giá)</label>
                <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label', $product->label) }}">
                @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-check mb-3">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Kích hoạt sản phẩm</label>
            </div>
            <div class="form-check mb-3">
                <input type="hidden" name="is_featured" value="0">
                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_featured">Sản phẩm nổi bật</label>
            </div>
        </div>
    </div>

    {{-- Quản lý hình ảnh --}}
    <div class="card mb-4">
        <div class="card-header">Hình ảnh sản phẩm</div>
        <div class="card-body">
            <h5>Ảnh chính</h5>
            <div class="mb-3">
                @php
                    $mainImage = $product->images->where('is_primary', true)->first();
                    $mainImageUrl = $mainImage ? (Str::startsWith($mainImage->image_url, 'http') ? $mainImage->image_url : asset('storage/' . $mainImage->image_url)) : 'https://placehold.co/150x100?text=No+Image';
                @endphp
                <img src="{{ $mainImageUrl }}" alt="Ảnh chính" width="150" class="mb-2">
                <label for="main_image_file" class="form-label">Upload ảnh chính mới</label>
                <input type="file" class="form-control @error('main_image_file') is-invalid @enderror" id="main_image_file" name="main_image_file">
                @error('main_image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="main_image_url" class="form-label">Hoặc URL ảnh chính</label>
                <input type="url" class="form-control @error('main_image_url') is-invalid @enderror" id="main_image_url" name="main_image_url" value="{{ old('main_image_url', $mainImage && Str::startsWith($mainImage->image_url, 'http') ? $mainImage->image_url : '') }}" placeholder="https://example.com/main.jpg">
                @error('main_image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <hr>

            <h5>Ảnh phụ</h5>
            <div id="additional-images-container">
                @foreach ($product->images->where('is_primary', false) as $index => $image)
                    <div class="input-group mb-2 existing-image-row">
                        <img src="{{ Str::startsWith($image->image_url, 'http') ? $image->image_url : asset('storage/' . $image->image_url) }}" alt="Ảnh phụ" width="80" class="me-2">
                        <input type="text" class="form-control" name="existing_images[{{ $image->id }}][url]" value="{{ $image->image_url }}" placeholder="URL ảnh phụ">
                        <input type="file" class="form-control" name="existing_images[{{ $image->id }}][file]">
                        <button type="button" class="btn btn-danger remove-existing-image" data-image-id="{{ $image->id }}">Xóa</button>
                        <input type="hidden" name="existing_images[{{ $image->id }}][delete]" value="0" class="delete-flag">
                    </div>
                @endforeach
            </div>

            <div class="mb-3">
                <label for="images_files" class="form-label">Thêm ảnh phụ mới (Upload file)</label>
                <input type="file" class="form-control @error('images_files.*') is-invalid @enderror" id="images_files" name="images_files[]" multiple>
                @error('images_files.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="images_urls" class="form-label">Thêm ảnh phụ mới (URL)</label>
                <input type="text" class="form-control @error('images_urls.*') is-invalid @enderror" id="images_urls" name="images_urls[]" placeholder="https://example.com/image1.jpg, https://example.com/image2.jpg (phân cách bằng dấu phẩy)">
                @error('images_urls.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    {{-- Quản lý biến thể --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Biến thể sản phẩm</h5>
            <button type="button" class="btn btn-success btn-sm" id="add-variant-btn">Thêm biến thể</button>
        </div>
        <div class="card-body">
            <div id="variants-container">
                {{-- Lặp qua các biến thể hiện có hoặc dữ liệu cũ nếu validation fail --}}
                @php
                    $variantsToRender = old('variants'); // Ưu tiên old data trước
                    if (empty($variantsToRender)) { // Nếu không có old data, dùng product->variants
                        $variantsToRender = $product->variants->toArray();
                    }
                    // Đảm bảo luôn là mảng để foreach không lỗi
                    $variantsToRender = is_array($variantsToRender) ? $variantsToRender : [];
                @endphp

                @forelse($variantsToRender as $index => $variantData)
                    <div class="card mb-3 variant-item" data-index="{{ $index }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa biến thể</button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control @error('variants.' . $index . '.sku') is-invalid @enderror"
                                       name="variants[{{ $index }}][sku]" value="{{ old('variants.' . $index . '.sku', $variantData['sku'] ?? '') }}" required>
                                @error('variants.' . $index . '.sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thuộc tính</label>
                                <div id="attributes-container-{{ $index }}">
                                    @php
                                        // $variantData['attributes'] có thể là một mảng hoặc null (từ DB)
                                        // Hoặc có thể là một mảng các object (từ old() khi form fail)
                                        $attributes = (array)($variantData['attributes'] ?? []);
                                        // Nếu old() trả về một mảng object cho attributes, cần chuyển đổi nó
                                        if (!empty($attributes) && is_array($attributes) && isset($attributes[0]) && is_object($attributes[0])) {
                                            $tempAttrs = [];
                                            foreach($attributes as $attrObj) {
                                                if (isset($attrObj->name) && isset($attrObj->value)) {
                                                    $tempAttrs[$attrObj->name] = $attrObj->value;
                                                }
                                            }
                                            $attributes = $tempAttrs;
                                        }

                                        $attrIndex = 0;
                                    @endphp
                                    @foreach($attributes as $key => $value)
                                        <div class="input-group mb-2 attribute-row">
                                            <input type="text" class="form-control @error('variants.' . $index . '.attributes.' . $attrIndex . '.name') is-invalid @enderror"
                                                   name="variants[{{ $index }}][attributes][{{ $attrIndex }}][name]" value="{{ $key }}" placeholder="Tên thuộc tính" required>
                                            <input type="text" class="form-control @error('variants.' . $index . '.attributes.' . $attrIndex . '.value') is-invalid @enderror"
                                                   name="variants[{{ $index }}][attributes][{{ $attrIndex }}][value]" value="{{ $value }}" placeholder="Giá trị" required>
                                            <button type="button" class="btn btn-danger btn-sm remove-attribute">Xóa</button>
                                        </div>
                                        @php $attrIndex++; @endphp
                                    @endforeach

                                    {{-- Nếu không có thuộc tính nào và không có old data cho thuộc tính đó, hiển thị một hàng trống --}}
                                    @if(empty($attributes) && empty(old('variants.' . $index . '.attributes')))
                                        <div class="input-group mb-2 attribute-row">
                                            <input type="text" class="form-control" name="variants[{{ $index }}][attributes][0][name]" value="" placeholder="Tên thuộc tính" required>
                                            <input type="text" class="form-control" name="variants[{{ $index }}][attributes][0][value]" value="" placeholder="Giá trị" required>
                                            <button type="button" class="btn btn-danger btn-sm remove-attribute">Xóa</button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm add-attribute-btn" data-variant-index="{{ $index }}">Thêm thuộc tính</button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Giá</label>
                                <input type="number" class="form-control @error('variants.' . $index . '.price') is-invalid @enderror"
                                       name="variants[{{ $index }}][price]" value="{{ old('variants.' . $index . '.price', $variantData['price'] ?? 0) }}" min="0" required>
                                @error('variants.' . $index . '.price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Giá khuyến mãi (nếu có)</label>
                                <input type="number" class="form-control @error('variants.' . $index . '.sale_price') is-invalid @enderror"
                                       name="variants[{{ $index }}][sale_price]" value="{{ old('variants.' . $index . '.sale_price', $variantData['sale_price'] ?? '') }}" min="0">
                                @error('variants.' . $index . '.sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="variants[{{ $index }}][is_main_variant]" value="0">
                                <input class="form-check-input is-main-variant-checkbox" type="checkbox"
                                       name="variants[{{ $index }}][is_main_variant]" value="1" id="is_main_variant_{{ $index }}"
                                       {{ old('variants.' . $index . '.is_main_variant', $variantData['is_main_variant'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_main_variant_{{ $index }}">Là biến thể chính</label>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Biến thể mặc định nếu không có biến thể nào --}}
                    <div class="card mb-3 variant-item" data-index="0">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control" name="variants[0][sku]" value="{{ old('variants.0.sku') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thuộc tính</label>
                                <div id="attributes-container-0">
                                    <div class="input-group mb-2 attribute-row">
                                        <input type="text" class="form-control" name="variants[0][attributes][0][name]" value="{{ old('variants.0.attributes.0.name') }}" placeholder="Tên thuộc tính" required>
                                        <input type="text" class="form-control" name="variants[0][attributes][0][value]" value="{{ old('variants.0.attributes.0.value') }}" placeholder="Giá trị" required>
                                        <button type="button" class="btn btn-danger btn-sm remove-attribute">Xóa</button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm add-attribute-btn" data-variant-index="0">Thêm thuộc tính</button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Giá</label>
                                <input type="number" class="form-control" name="variants[0][price]" value="{{ old('variants.0.price', 0) }}" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Giá khuyến mãi (nếu có)</label>
                                <input type="number" class="form-control" name="variants[0][sale_price]" value="{{ old('variants.0.sale_price') }}" min="0">
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="variants[0][is_main_variant]" value="0">
                                <input class="form-check-input is-main-variant-checkbox" type="checkbox"
                                       name="variants[0][is_main_variant]" value="1" id="is_main_variant_0" checked>
                                <label class="form-check-label" for="is_main_variant_0">Là biến thể chính</label>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
            <template id="variant-template">
                <div class="card mb-3 variant-item">
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-2">
                            <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa biến thể</button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" class="form-control" name="variants[INDEX][sku]" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thuộc tính</label>
                            <div id="attributes-container-INDEX">
                                <div class="input-group mb-2 attribute-row">
                                    <input type="text" class="form-control" name="variants[INDEX][attributes][0][name]" placeholder="Tên thuộc tính" required>
                                    <input type="text" class="form-control" name="variants[INDEX][attributes][0][value]" placeholder="Giá trị" required>
                                    <button type="button" class="btn btn-danger btn-sm remove-attribute">Xóa</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm add-attribute-btn" data-variant-index="INDEX">Thêm thuộc tính</button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá</label>
                            <input type="number" class="form-control" name="variants[INDEX][price]" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá khuyến mãi (nếu có)</label>
                            <input type="number" class="form-control" name="variants[INDEX][sale_price]" min="0">
                        </div>
                        <div class="form-check">
                            <input type="hidden" name="variants[INDEX][is_main_variant]" value="0">
                            <input class="form-check-input is-main-variant-checkbox" type="checkbox" name="variants[INDEX][is_main_variant]" value="1">
                            <label class="form-check-label">Là biến thể chính</label>
                        </div>
                    </div>
                </div>
            </template>
            <template id="attribute-template">
                <div class="input-group mb-2 attribute-row">
                    <input type="text" class="form-control" name="variants[VARIANT_INDEX][attributes][ATTR_INDEX][name]" placeholder="Tên thuộc tính" required>
                    <input type="text" class="form-control" name="variants[VARIANT_INDEX][attributes][ATTR_INDEX][value]" placeholder="Giá trị" required>
                    <button type="button" class="btn btn-danger btn-sm remove-attribute">Xóa</button>
                </div>
            </template>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const variantsContainer = document.getElementById('variants-container');
    const addVariantBtn = document.getElementById('add-variant-btn');
    const variantTemplate = document.getElementById('variant-template');
    const attributeTemplate = document.getElementById('attribute-template');

    // Khởi tạo variantIndex dựa trên số lượng biến thể hiện tại (từ DB hoặc old input)
    let variantIndex = variantsContainer.querySelectorAll('.variant-item').length;
    if (variantIndex === 0) { // Nếu không có biến thể nào được render mặc định (ví dụ trang tạo mới)
        addVariant(); // Thêm một biến thể mặc định
    }

    function updateVariantIndices() {
        variantsContainer.querySelectorAll('.variant-item').forEach((item, i) => {
            item.dataset.index = i;
            item.querySelectorAll('[name^="variants["]').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/variants\[\d+\]/, `variants[${i}]`));
                }
            });
            item.querySelector('.add-attribute-btn').dataset.variantIndex = i;

            // Update attribute indices within each variant
            item.querySelectorAll('.attribute-row').forEach((attrRow, j) => {
                attrRow.querySelectorAll('[name^="variants[' + i + '][attributes]"]').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/attributes\[\d+\]/, `attributes[${j}]`));
                    }
                });
            });

            // Update ID for main variant checkbox
            const mainCheckbox = item.querySelector('.is-main-variant-checkbox');
            if (mainCheckbox) {
                mainCheckbox.id = `is_main_variant_${i}`;
                mainCheckbox.nextElementSibling.setAttribute('for', `is_main_variant_${i}`);
            }
        });
        // Sau khi cập nhật lại indices, cập nhật lại biến global variantIndex
        variantIndex = variantsContainer.querySelectorAll('.variant-item').length;
    }

    function addVariant(initialData = null) {
        const newVariant = variantTemplate.content.cloneNode(true).firstElementChild;
        const currentVariantIndex = variantIndex; // Sử dụng biến cục bộ để tránh bị thay đổi trong quá trình xử lý

        newVariant.dataset.index = currentVariantIndex;

        // Cập nhật tất cả các thuộc tính 'name' trong template
        newVariant.querySelectorAll('[name*="INDEX"]').forEach(input => {
            input.setAttribute('name', input.getAttribute('name').replace(/INDEX/, currentVariantIndex));
        });
        // Cập nhật ID của attributes-container
        newVariant.querySelector('[id*="attributes-container-INDEX"]').id = `attributes-container-${currentVariantIndex}`;
        // Cập nhật data-variant-index cho nút thêm thuộc tính
        newVariant.querySelector('.add-attribute-btn').dataset.variantIndex = currentVariantIndex;


        // Set initial values if provided (for old input on validation error or existing variants)
        if (initialData) {
            newVariant.querySelector('[name$="[sku]"]').value = initialData.sku ?? '';
            newVariant.querySelector('[name$="[price]"]').value = initialData.price ?? 0;
            newVariant.querySelector('[name$="[sale_price]"]').value = initialData.sale_price ?? '';
            newVariant.querySelector('[name$="[is_main_variant]"]').checked = initialData.is_main_variant ?? false;

            // Handle attributes
            const attributesContainer = newVariant.querySelector(`#attributes-container-${currentVariantIndex}`);
            attributesContainer.innerHTML = ''; // Clear default attribute row from template

            // Ensure initialData.attributes is an array before iterating
            // When old() is used, attributes might come as an array of objects ({name: 'x', value: 'y'})
            // When from Eloquent, attributes is an associative array (key => value)
            let initialAttributes = initialData.attributes;
            if (!initialAttributes || (Array.isArray(initialAttributes) && initialAttributes.length === 0)) {
                initialAttributes = {}; // Ensure it's an empty object if no attributes
            } else if (Array.isArray(initialAttributes) && initialAttributes.length > 0 && typeof initialAttributes[0] === 'object' && initialAttributes[0] !== null && 'name' in initialAttributes[0]) {
                // This converts old() array of objects back to associative array
                let tempAttrs = {};
                initialAttributes.forEach(attrObj => {
                    if (attrObj && 'name' in attrObj && 'value' in attrObj) {
                        tempAttrs[attrObj.name] = attrObj.value;
                    }
                });
                initialAttributes = tempAttrs;
            }

            if (Object.keys(initialAttributes).length > 0) {
                let attrIdx = 0;
                for (const key in initialAttributes) {
                    const value = initialAttributes[key];
                    const newAttributeRow = attributeTemplate.content.cloneNode(true).firstElementChild;
                    newAttributeRow.querySelectorAll('input').forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            input.setAttribute('name', name.replace(/VARIANT_INDEX/, currentVariantIndex).replace(/ATTR_INDEX/, attrIdx));
                        }
                    });
                    newAttributeRow.querySelector('[name$="[name]"]').value = key;
                    newAttributeRow.querySelector('[name$="[value]"]').value = value;
                    attributesContainer.appendChild(newAttributeRow);
                    attrIdx++;
                }
            } else {
                // If no attributes, add one empty row
                const newAttributeRow = attributeTemplate.content.cloneNode(true).firstElementChild;
                newAttributeRow.querySelectorAll('input').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/VARIANT_INDEX/, currentVariantIndex).replace(/ATTR_INDEX/, 0));
                    }
                });
                attributesContainer.appendChild(newAttributeRow);
            }
        } else {
            // For brand new variant (no initialData), ensure default attribute row has correct names
            const attributesContainer = newVariant.querySelector('[id^="attributes-container-"]');
            const defaultAttributeRow = attributesContainer.querySelector('.attribute-row');
            if (defaultAttributeRow) {
                defaultAttributeRow.querySelectorAll('input').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/INDEX/, currentVariantIndex));
                    }
                });
            }
        }

        // Update ID for main variant checkbox
        const mainCheckbox = newVariant.querySelector('.is-main-variant-checkbox');
        if (mainCheckbox) {
            mainCheckbox.id = `is_main_variant_${currentVariantIndex}`;
            mainCheckbox.nextElementSibling.setAttribute('for', `is_main_variant_${currentVariantIndex}`);
        }

        variantsContainer.appendChild(newVariant);
        // Do NOT increment variantIndex here. It's handled by updateVariantIndices on initial render and adds.
        updateVariantIndices(); // Re-index all variants after adding. This will also update variantIndex.
    }

    // Handle adding new variant
    addVariantBtn.addEventListener('click', () => addVariant());

    // Handle removing variant
    variantsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variant')) {
            if (variantsContainer.querySelectorAll('.variant-item').length > 1) {
                e.target.closest('.variant-item').remove();
                updateVariantIndices();
                // Re-evaluate main variant status after removal
                handleMainVariantCheckboxes();
            } else {
                alert('Sản phẩm phải có ít nhất một biến thể.');
            }
        }
    });

    // Handle adding attribute for a specific variant
    variantsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-attribute-btn')) {
            const variantItem = e.target.closest('.variant-item');
            const variantIdx = variantItem.dataset.index;
            const attributesContainer = variantItem.querySelector(`#attributes-container-${variantIdx}`);
            let attrIndex = attributesContainer.querySelectorAll('.attribute-row').length;

            const newAttributeRow = attributeTemplate.content.cloneNode(true).firstElementChild;
            newAttributeRow.querySelectorAll('input').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/VARIANT_INDEX/, variantIdx).replace(/ATTR_INDEX/, attrIndex));
                }
            });
            attributesContainer.appendChild(newAttributeRow);
        }
    });

    // Handle removing attribute
    variantsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-attribute')) {
            const attributeRow = e.target.closest('.attribute-row');
            const attributesContainer = attributeRow.closest('[id^="attributes-container-"]');
            // Allow removing only if more than one attribute row exists
            if (attributesContainer.querySelectorAll('.attribute-row').length > 1) {
                attributeRow.remove();
                // Re-index attributes within this variant after removal
                const variantItem = attributesContainer.closest('.variant-item');
                const variantIdx = variantItem.dataset.index;
                attributesContainer.querySelectorAll('.attribute-row').forEach((row, j) => {
                    row.querySelectorAll('input').forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            input.setAttribute('name', name.replace(/attributes\[\d+\]/, `attributes[${j}]`));
                        }
                    });
                });
            } else {
                alert('Mỗi biến thể phải có ít nhất một thuộc tính.');
            }
        }
    });

    // Handle main variant checkbox logic (only one can be checked)
    function handleMainVariantCheckboxes() {
        variantsContainer.querySelectorAll('.is-main-variant-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                if (e.target.checked) {
                    // Uncheck all other main variant checkboxes
                    variantsContainer.querySelectorAll('.is-main-variant-checkbox').forEach(otherCheckbox => {
                        if (otherCheckbox !== e.target) {
                            otherCheckbox.checked = false;
                        }
                    });
                } else {
                    // If the last main variant is unchecked, check the first one
                    const checkedCount = variantsContainer.querySelectorAll('.is-main-variant-checkbox:checked').length;
                    if (checkedCount === 0 && variantsContainer.querySelectorAll('.is-main-variant-checkbox').length > 0) {
                        variantsContainer.querySelector('.is-main-variant-checkbox').checked = true;
                    }
                }
            });
        });
    }

    // Initial load logic for variants
    const initialLoadVariants = () => {
        variantsContainer.innerHTML = ''; // Clear any default empty variant from template
        const initialVariantsData = @json(old('variants', $product->variants->toArray()));
        
        if (initialVariantsData.length > 0) {
            initialVariantsData.forEach(variantData => {
                addVariant(variantData);
            });
        } else {
            addVariant(); // Add one default variant if none exist
        }
        updateVariantIndices(); // Final re-indexing after all initial variants are added
        handleMainVariantCheckboxes(); // Attach listeners and ensure one is checked
    };

    initialLoadVariants(); // Call this function to initialize variants on page load
});
</script>
@endpush