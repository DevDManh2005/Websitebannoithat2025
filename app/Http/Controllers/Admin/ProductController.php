<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm.
     */
    public function index()
    {
        $products = Product::with(['categories', 'brand', 'variants'])
            ->latest()
            ->paginate(15);

        return view('admins.products.index', compact('products'));
    }

    /**
     * Hiển thị form tạo sản phẩm mới.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $brands     = Brand::active()->orderBy('name')->get();
        $suppliers  = Supplier::active()->orderBy('name')->get();
        return view('admins.products.create', compact('categories', 'brands', 'suppliers'));
    }

    /**
     * Lưu sản phẩm mới vào database.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
            'label' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'main_image_file' => 'nullable|image|max:4096',
            'main_image_url' => 'nullable|url|max:255',
            'images_files' => 'nullable|array',
            'images_files.*' => 'nullable|image|max:4096',
            'images_urls' => 'nullable|array',
            'images_urls.*' => 'nullable|string|max:255',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|string|max:255|unique:product_variants,sku',
            'variants.*.weight' => 'required|integer|min:0',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.is_main_variant' => 'nullable|boolean',
            'variants.*.attributes' => 'required|array',
            'variants.*.attributes.*.name' => 'required|string|max:255',
            'variants.*.attributes.*.value' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $data) {
            $productData = $request->except(['_token', 'categories', 'variants', 'main_image_file', 'main_image_url', 'images_files', 'images_urls']);
            $productData['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
            $productData['is_active'] = $request->has('is_active');
            $productData['is_featured'] = $request->has('is_featured');

            // 1. Tạo sản phẩm
            $product = Product::create($productData);

            // 2. Gán nhiều danh mục
            $product->categories()->sync($data['categories']);

            // 3. Xử lý ảnh chính
            $mainImageUrl = null;
            if ($request->hasFile('main_image_file')) {
                $mainImageUrl = $request->file('main_image_file')->store('products', 'public');
            } elseif (!empty($data['main_image_url'])) {
                $mainImageUrl = $data['main_image_url'];
            }
            if ($mainImageUrl) {
                $product->images()->create(['image_url' => $mainImageUrl, 'is_primary' => true]);
            }

            // 4. Xử lý ảnh phụ
            if ($request->hasFile('images_files')) {
                foreach ($request->file('images_files') as $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create(['image_url' => $path, 'is_primary' => false]);
                }
            }
            if (!empty($data['images_urls'][0])) {
                $urls = is_string($data['images_urls'][0]) ? explode(',', $data['images_urls'][0]) : $data['images_urls'];
                foreach ($urls as $url) {
                    if (trim($url)) {
                        $product->images()->create(['image_url' => trim($url), 'is_primary' => false]);
                    }
                }
            }

            // 5. Tạo biến thể
            foreach ($data['variants'] as $variantData) {
                $attributes = [];
                foreach ($variantData['attributes'] as $attr) {
                    if (!empty($attr['name']) && !empty($attr['value'])) {
                        $attributes[$attr['name']] = $attr['value'];
                    }
                }
                $variant = $product->variants()->create([
                    'sku' => $variantData['sku'],
                    'attributes' => $attributes,
                    'price' => $variantData['price'],
                    'sale_price' => $variantData['sale_price'],
                    'weight' => $variantData['weight'],
                    'is_main_variant' => $variantData['is_main_variant'] ?? 0,
                ]);

                // 6. Tạo bản ghi kho hàng cho mỗi biến thể
                Inventory::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => 0, // Mặc định số lượng là 0
                ]);
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Tạo sản phẩm thành công');
    }

    public function show(Product $product)
    {
        $product->load(['categories', 'brand', 'supplier', 'images', 'variants']);
        return view('admins.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('categories');
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();
        return view('admins.products.edit', compact('product', 'categories', 'brands', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'slug' => "nullable|string|max:255|unique:products,slug,{$product->id}",
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|string|max:255',
            'variants.*.weight' => 'required|integer|min:0',
            'variants.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $product, $data) {
            $productData = $request->except(['_token', '_method', 'categories', 'variants', 'main_image_file', 'main_image_url', 'images_files', 'images_urls', 'existing_images']);
            $productData['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
            $productData['is_active'] = $request->has('is_active');
            $productData['is_featured'] = $request->has('is_featured');
            
            $product->update($productData);
            $product->categories()->sync($data['categories']);

            // Cập nhật biến thể (Xóa cũ tạo mới)
            $product->variants()->delete();
            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    $attributes = [];
                    foreach ($variantData['attributes'] as $attr) {
                        if (!empty($attr['name']) && !empty($attr['value'])) {
                            $attributes[$attr['name']] = $attr['value'];
                        }
                    }
                    $product->variants()->create([
                        'sku' => $variantData['sku'],
                        'attributes' => $attributes,
                        'price' => $variantData['price'],
                        'sale_price' => $variantData['sale_price'],
                        'weight' => $variantData['weight'],
                        'is_main_variant' => $variantData['is_main_variant'] ?? 0,
                    ]);
                }
            }
            
            // Cập nhật ảnh chính
            $mainImageUrl = null;
            if ($request->hasFile('main_image_file')) {
                $mainImageUrl = $request->file('main_image_file')->store('products', 'public');
            } elseif (!empty($request->main_image_url)) {
                $mainImageUrl = $request->main_image_url;
            }
            if ($mainImageUrl) {
                $oldMain = $product->images()->where('is_primary', true)->first();
                if ($oldMain) {
                    if (!Str::startsWith($oldMain->image_url, 'http')) Storage::disk('public')->delete($oldMain->image_url);
                    $oldMain->delete();
                }
                $product->images()->create(['image_url' => $mainImageUrl, 'is_primary' => true]);
            }

            // Cập nhật ảnh phụ
            $product->images()->where('is_primary', false)->get()->each(function($image) {
                if (!Str::startsWith($image->image_url, 'http')) Storage::disk('public')->delete($image->image_url);
                $image->delete();
            });
            if ($request->hasFile('images_files')) {
                foreach ($request->file('images_files') as $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create(['image_url' => $path, 'is_primary' => false]);
                }
            }
            if (!empty($request->images_urls[0])) {
                $urls = is_string($request->images_urls[0]) ? explode(',', $request->images_urls[0]) : $request->images_urls;
                foreach ($urls as $url) {
                    if (trim($url)) {
                        $product->images()->create(['image_url' => trim($url), 'is_primary' => false]);
                    }
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            if (!Str::startsWith($img->image_url, ['http://', 'https://'])) {
                Storage::disk('public')->delete($img->image_url);
            }
        }
        $product->delete();
        return back()->with('success', 'Xóa sản phẩm thành công');
    }

    /**
     * API để lấy danh sách biến thể và thông tin kho hàng của một sản phẩm.
     */
    public function getVariantsWithInventory(Product $product)
    {
        // Tải sẵn thông tin kho hàng và vị trí cho mỗi biến thể
        $variants = $product->variants()->with(['inventory.location'])->get();
        return response()->json($variants);
    }
}
