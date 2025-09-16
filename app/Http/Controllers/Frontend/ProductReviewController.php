<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductReview;

class ProductReviewController extends Controller
{
    protected array $bannedWords = [
        'đồ ngu', 'ngu', 'ngu dốt', 'chửi', 'bậy',
        'lừa đảo',
    ];

    protected function censor(string $text): string
    {
        foreach ($this->bannedWords as $w) {
            $w = trim($w);
            if ($w === '') continue;
            $pattern = '/(?<!\p{L})' . preg_quote($w, '/') . '(?!\p{L})/iu';
            $text = preg_replace_callback($pattern, function ($m) {
                $len = mb_strlen($m[0], 'UTF-8');
                return str_repeat('*', $len);
            }, $text);
        }
        return $text;
    }

    protected function isStaffOrAdmin(): bool
    {
        $role = optional(optional(Auth::user())->role)->name ?? '';
        return Auth::check() && in_array($role, ['admin', 'nhanvien'], true);
    }

    protected function historyPath(ProductReview $review): string
    {
        return storage_path('app/reviews_history/'.$review->id.'.json');
    }

    protected function appendHistory(ProductReview $review, array $entry): void
    {
        $path = $this->historyPath($review);
        $dir  = dirname($path);
        if (!is_dir($dir)) @mkdir($dir, 0775, true);

        $now = now()->toIso8601String();
        $base = [
            'ts'  => $now,
            'by'  => ['id' => Auth::id(), 'name' => optional(Auth::user())->name ?? ''],
            'rid' => $review->id,
            'pid' => $review->product_id,
        ];

        $data = [];
        if (file_exists($path)) {
            $json = @file_get_contents($path);
            $data = json_decode($json ?: '[]', true) ?: [];
        }

        $data[] = array_merge($base, $entry);
        @file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function store(Request $request, Product $product)
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'hasReviewedProduct') && $user->hasReviewedProduct($product->id)) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        if (!$this->isStaffOrAdmin()) {
            // Nếu user chưa đăng nhập -> báo lỗi yêu cầu đăng nhập
            if (!$user) {
                return back()->with('error', 'Vui lòng đăng nhập để đánh giá sản phẩm.');
            }
            if (method_exists($user, 'hasPurchasedProduct') && !$user->hasPurchasedProduct($product->id)) {
                return back()->with('error', 'Bạn chưa mua sản phẩm này nên không thể đánh giá.');
            }
        }

        $request->validate([
            'image'  => 'nullable|image|max:2048',
            'review' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // store on public disk so views can use asset('storage/...') consistently
            $imagePath = $request->file('image')->store('reviews_images', 'public');
        }

        $data = [
            'product_id' => $product->id,
            'user_id'    => Auth::id(),
            'rating'     => $request->input('rating', 0),
            'review'     => $this->censor($request->input('review', '')),
            'image'      => $imagePath,
            'approved'   => $this->isStaffOrAdmin() ? 1 : 0,
        ];

        $review = ProductReview::create($data);
        $this->appendHistory($review, ['action' => 'create', 'data' => $data]);

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá.');
    }

    /**
     * Trả lời một đánh giá (route: POST /reviews/{review}/reply)
     * IMPORTANT: ensure route param name is {review} and route name reviews.reply
     */
    public function reply(Request $request, ProductReview $review)
    {
        $user = Auth::user();

        $request->validate(['review' => 'required|string']);

        $productId = $review->product_id;

        if (!$this->isStaffOrAdmin()) {
            if (!$user) {
                return back()->with('error', 'Vui lòng đăng nhập để trả lời đánh giá.');
            }
            if (method_exists($user, 'hasPurchasedProduct') && !$user->hasPurchasedProduct($productId)) {
                return back()->with('error', 'Bạn không có quyền trả lời đánh giá này.');
            }
        }

        $censored = $this->censor($request->input('review', ''));
        $storedReview = "[reply:#{$review->id}]{$censored}";

        $data = [
            'product_id' => $productId,
            'user_id'    => Auth::id(),
            'rating'     => 0,
            'review'     => $storedReview,
            'image'      => null,
            'approved'   => $this->isStaffOrAdmin() ? 1 : 0,
        ];

        $reply = ProductReview::create($data);
        $this->appendHistory($reply, ['action' => 'reply', 'parent' => $review->id, 'data' => $data]);

        return back()->with('success', 'Đã gửi phản hồi.');
    }

    public function update(Request $request, ProductReview $review)
    {
        $userId = Auth::id();
        $isOwner = $userId === $review->user_id;
        if (!$isOwner && !$this->isStaffOrAdmin()) {
            return back()->with('error', 'Bạn không có quyền sửa đánh giá này.');
        }

        $request->validate([
            'review' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'image'  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('reviews_images', 'public');
            if ($review->image) {
                @Storage::disk('public')->delete($review->image);
            }
            $review->image = $path;
        }

        $raw = $request->input('review', '');
        if (preg_match('/^\[reply:#\d+\]/', $review->review)) {
            preg_match('/^(\[reply:#\d+\])(.+)$/s', $review->review, $parts);
            $prefix = $parts[1] ?? '';
            $review->review = $prefix . $this->censor($raw);
        } else {
            $review->review = $this->censor($raw);
        }

        if ($request->filled('rating')) {
            $review->rating = $request->input('rating');
        }

        $review->save();
        $this->appendHistory($review, ['action' => 'update', 'data' => ['review' => $review->review, 'rating' => $review->rating]]);

        return back()->with('success', 'Đã cập nhật đánh giá.');
    }

    public function destroy(ProductReview $review)
    {
        $userId = Auth::id();
        $isOwner = $userId === $review->user_id;
        if (!$isOwner && !$this->isStaffOrAdmin()) {
            return back()->with('error', 'Bạn không có quyền xoá đánh giá này.');
        }

        $this->appendHistory($review, ['action' => 'delete', 'data' => ['review' => $review->review, 'rating' => $review->rating ?? null]]);
        if ($review->image) {
            @Storage::disk('public')->delete($review->image);
        }
        $review->delete();

        return back()->with('success', 'Đã xoá đánh giá.');
    }
}