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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
   public function index()
{
    $products = Product::with(['category', 'brand', 'supplier', 'images', 'variants'])
                       ->latest()
                       ->paginate(15);

    return view('admins.products.index', compact('products'));
}

    public function create()
    {
        $categories = Category::active()->get();
        $brands     = Brand::active()->get();
        $suppliers  = Supplier::active()->get();

        return view('admins.products.create', compact('categories', 'brands', 'suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'nullable|string|max:255|unique:products,slug',
            'category_id'     => 'required|exists:categories,id',
            'brand_id'        => 'nullable|exists:brands,id',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'description'     => 'nullable|string',
            'label'           => 'nullable|string|max:100',
            'is_active'       => 'boolean',
            'is_featured'     => 'boolean',
            'main_image_file' => 'nullable|image|max:4096',
            'main_image_url'  => 'nullable|url|max:255',
            'images_files'    => 'nullable|array',
            'images_files.*'  => 'nullable|image|max:4096',
            'images_urls'     => 'nullable|array',
            'images_urls.*'   => 'nullable|url|max:255',
            'variants'        => 'nullable|array',
            'variants.*.sku'  => 'required|string|max:255',
            'variants.*.attributes' => 'required|array',
            'variants.*.attributes.*.name' => 'required|string|max:255',
            'variants.*.attributes.*.value' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.is_main_variant' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        // Tạo sản phẩm
        $product = Product::create($data);

        // Xử lý ảnh chính
        $main = null;
        if ($request->hasFile('main_image_file')) {
            $main = $request->file('main_image_file')->store('products', 'public');
        } elseif (!empty($data['main_image_url'])) {
            $main = $data['main_image_url'];
        }

        if ($main) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_url'  => $main,
                'is_primary' => true,
            ]);
        }

        // Xử lý ảnh phụ
        if ($request->hasFile('images_files')) {
            foreach ($request->file('images_files') as $file) {
                if ($file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url'  => $path,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        if (!empty($data['images_urls'])) {
            foreach ($data['images_urls'] as $url) {
                if ($url) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url'  => $url,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        // Tạo biến thể
        if (!empty($data['variants'])) {
            foreach ($data['variants'] as $variantData) {
                $attributes = [];
                foreach ($variantData['attributes'] as $attr) {
                    $attributes[$attr['name']] = $attr['value'];
                }
                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantData['sku'],
                    'attributes' => $attributes,
                    'price' => $variantData['price'],
                    'sale_price' => $variantData['sale_price'],
                    'is_main_variant' => $variantData['is_main_variant'] ?? false,
                ]);
            }
        } else {
            // Tạo biến thể mặc định nếu không có
            ProductVariant::create([
                'product_id' => $product->id,
                'sku' => Str::slug($product->name) . '-main',
                'attributes' => [],
                'price' => 0,
                'sale_price' => null,
                'is_main_variant' => true,
            ]);
        }

        // Tạo inventory
        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 0,
        ]);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Tạo sản phẩm thành công');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'supplier', 'images', 'variants']);
        return view('admins.products.show', compact('product'));
    }

    public function edit(Request $request, Product $product)
    {
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $suppliers = Supplier::active()->get();
        $product->load(['images', 'variants']);
        $oldInput = $request->old();

        return view('admins.products.edit', compact('product', 'categories', 'brands', 'suppliers', 'oldInput'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => "nullable|string|max:255|unique:products,slug,{$product->id}",
            'category_id' => 'required|exists:categories,id',
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
            'images_urls.*' => 'nullable|url|max:255',
            'variants' => 'nullable|array',
            'variants.*.sku' => 'required|string|max:255',
            'variants.*.attributes' => 'required|array',
            'variants.*.attributes.*.name' => 'required|string|max:255',
            'variants.*.attributes.*.value' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.is_main_variant' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        // Cập nhật sản phẩm
        $product->update($data);

        // Xử lý ảnh chính
        $main = null;
        if ($request->hasFile('main_image_file')) {
            $main = $request->file('main_image_file')->store('products', 'public');
        } elseif (!empty($data['main_image_url'])) {
            $main = $data['main_image_url'];
        }

        if ($main) {
            if ($old = $product->images()->where('is_primary', true)->first()) {
                if (!Str::startsWith($old->image_url, ['http://', 'https://'])) {
                    Storage::disk('public')->delete($old->image_url);
                }
                $old->delete();
            }
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $main,
                'is_primary' => true,
            ]);
        }

        // Xử lý ảnh phụ
        $oldImages = $product->images()->where('is_primary', false)->get();
        foreach ($oldImages as $oldImage) {
            if (!Str::startsWith($oldImage->image_url, ['http://', 'https://'])) {
                Storage::disk('public')->delete($oldImage->image_url);
            }
            $oldImage->delete();
        }

        if ($request->hasFile('images_files')) {
            foreach ($request->file('images_files') as $file) {
                if ($file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $path,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        if (!empty($data['images_urls'])) {
            foreach ($data['images_urls'] as $url) {
                if ($url) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $url,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        // Xử lý biến thể
        $product->variants()->delete();
        if (!empty($data['variants'])) {
            foreach ($data['variants'] as $variantData) {
                $attributes = [];
                foreach ($variantData['attributes'] as $attr) {
                    $attributes[$attr['name']] = $attr['value'];
                }
                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantData['sku'],
                    'attributes' => $attributes,
                    'price' => $variantData['price'],
                    'sale_price' => $variantData['sale_price'],
                    'is_main_variant' => $variantData['is_main_variant'] ?? false,
                ]);
            }
        } else {
            // Tạo biến thể mặc định nếu không có
            ProductVariant::create([
                'product_id' => $product->id,
                'sku' => Str::slug($product->name) . '-main',
                'attributes' => [],
                'price' => 0,
                'sale_price' => null,
                'is_main_variant' => true,
            ]);
        }

        // Cập nhật inventory
        $inventory = Inventory::where('product_id', $product->id)->first();
        if (!$inventory) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => 0,
            ]);
        }

        return redirect()->route('admin.products.index')
                         ->with('success', 'Cập nhật sản phẩm thành công');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            if (!Str::startsWith($img->image_url, ['http://', 'https://'])) {
                Storage::disk('public')->delete($img->image_url);
            }
            $img->delete();
        }
        $product->variants()->delete();
        $product->inventories()->delete();
        $product->delete();

        return back()->with('success', 'Xóa sản phẩm thành công');
    }
}