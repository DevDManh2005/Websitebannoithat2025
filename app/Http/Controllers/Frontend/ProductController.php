<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm + bộ lọc + sắp xếp
     */
    public function index(Request $request)
    {
        $selectedCategories = array_map('intval', (array) $request->input('categories', []));

        $query = Product::query()
            ->active()
            ->with(['variants', 'images', 'categories']); // eager để hiển thị card nhanh

        /* ============ FILTERS ============ */

        // Danh mục (nhiều id)
        if (!empty($selectedCategories)) {
            $query->whereHas('categories', function ($q) use ($selectedCategories) {
                $q->whereIn('categories.id', $selectedCategories);
            });
        }

        // Thương hiệu
        if ($request->filled('brands')) {
            $query->whereIn('brand_id', (array) $request->brands);
        }

        // Giá tối đa (lọc theo biến thể)
        if ($request->filled('price_max')) {
            $priceMax = (int) $request->price_max;
            $query->whereHas('variants', function ($q) use ($priceMax) {
                $q->where('price', '<=', $priceMax);
            });
        }

        // Chỉ sản phẩm đang khuyến mãi (quick-link on_sale=1)
        if ($request->boolean('on_sale')) {
            $query->whereHas('variants', function ($q) {
                $q->whereNotNull('sale_price')
                  ->where('sale_price', '>', 0)
                  ->whereColumn('sale_price', '<', 'price');
            });
        }

        /* ============ SORTS ============ */

        $sort = (string) $request->query('sort', 'latest');

        switch ($sort) {
            case 'bestseller':
                // Subquery: tổng số lượng bán theo product_id
                $soldSub = DB::table('order_items as oi')
                    ->join('product_variants as pv2', 'pv2.id', '=', 'oi.product_variant_id')
                    ->join('orders as o', 'o.id', '=', 'oi.order_id')
                    ->where('o.status', '<>', 'cancelled')
                    ->select('pv2.product_id', DB::raw('SUM(oi.quantity) as sold_qty'))
                    ->groupBy('pv2.product_id');

                // Lưu ý: KHÔNG join product_variants ở ngoài để tránh nhân bản hàng
                // KHÔNG groupBy ngoài; chỉ select products.* + sold_qty là đủ cho Postgres
                $query->leftJoinSub($soldSub, 'sold', function ($join) {
                        $join->on('sold.product_id', '=', 'products.id');
                    })
                    ->select('products.*', DB::raw('COALESCE(sold.sold_qty, 0) as sold_qty'))
                    ->orderByDesc('sold_qty')
                    ->orderByDesc('products.created_at'); // phụ để ổn định
                break;

            case 'price_asc':
            case 'price_desc':
                // Sort theo giá của biến thể chính (một dòng duy nhất mỗi sản phẩm)
                $query->select('products.*')
                    ->join('product_variants as pv', function ($j) {
                        $j->on('pv.product_id', '=', 'products.id')
                          ->where('pv.is_main_variant', true);
                    });

                // Nếu có sale_price > 0 thì lấy sale_price, không thì lấy price
                $sortCol = DB::raw('COALESCE(NULLIF(pv.sale_price,0), pv.price)');

                $query->orderBy($sortCol, $sort === 'price_asc' ? 'asc' : 'desc')
                      ->orderByDesc('products.created_at');
                break;

            case 'new':     // alias của latest
            case 'latest':
            default:
                $query->orderByDesc('products.created_at');
                break;
        }

        $products = $query->paginate(12)->appends($request->query());

        /* ============ DATA FOR FILTER SIDEBAR ============ */

        $filterCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        $filterBrands = Brand::where('is_active', true)->get();

        $maxPrice = (int) ProductVariant::max('price');

        return view('frontend.products.index', [
            'products'           => $products,
            'categories'         => $filterCategories,
            'brands'             => $filterBrands,
            'max_price'          => $maxPrice ?: 50_000_000,
            'selectedCategories' => $selectedCategories,
        ]);
    }

    /**
     * Chi tiết sản phẩm.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with([
                'categories',
                'brand',
                'images',
                'variants',
                'approvedReviews.user.profile',
            ])
            ->firstOrFail();

        // Gom các tùy chọn thuộc tính (màu, size, ...)
        $attributeGroups = [];
        foreach ($product->variants as $variant) {
            foreach ((array) $variant->attributes as $key => $value) {
                $attributeGroups[$key] = $attributeGroups[$key] ?? [];
                if (!in_array($value, $attributeGroups[$key])) {
                    $attributeGroups[$key][] = $value;
                }
            }
        }

        // Sản phẩm liên quan (cùng danh mục)
        $relatedProducts = Product::whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->where('products.id', '!=', $product->id)
            ->active()
            ->with(['variants', 'images'])
            ->latest()
            ->take(4)
            ->get();

        // Quyền review & wishlist
        $userHasPurchased = false;
        $isWished = false;

        if (auth()->check()) {
            $user = auth()->user();

            // Admin/staff thì cho review luôn
            if (in_array($user->role_id, [1, 2])) {
                $userHasPurchased = true;
            } else {
                $userHasPurchased = $user->orders()
                    ->where('status', '!=', 'cancelled')
                    ->whereHas('items', function ($q) use ($product) {
                        $q->whereIn('product_variant_id', $product->variants->pluck('id'));
                    })
                    ->exists();
            }

            $isWished = $user->wishlist()->where('product_id', $product->id)->exists();
        }

        return view('frontend.products.show', compact(
            'product',
            'attributeGroups',
            'relatedProducts',
            'userHasPurchased',
            'isWished'
        ));
    }

    /**
     * Trang kết quả tìm kiếm đơn giản theo tên.
     */
    public function search(Request $request)
    {
        $query = (string) $request->input('q');

        $products = Product::where('name', 'like', "%{$query}%")
            ->with(['variants', 'images'])
            ->paginate(16);

        return view('frontend.search.index', compact('products', 'query'));
    }
}
