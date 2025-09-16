<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductReviewController extends Controller
{
   /** Danh sách từ cấm (tùy chỉnh thêm/bớt) */
    protected array $bannedWords = [
        'đồ ngu', 'ngu', 'ngu dốt', 'chửi', 'bậy',
        'lừa đảo', // ví dụ các từ nhạy cảm
    ];

    /** Thay từ cấm bằng dấu * (ưu tiên không thay phần trong từ khác) */
    protected function censor(string $text): string
    {
        foreach ($this->bannedWords as $w) {
            $w = trim($w);
            if ($w === '') continue;
            // đảm bảo không thay phần của từ khác: kiểm tra biên giới chữ bằng \p{L}
            $pattern = '/(?<!\p{L})' . preg_quote($w, '/') . '(?!\p{L})/iu';
            $text = preg_replace_callback($pattern, function($m){
                $len = mb_strlen($m[0], 'UTF-8');
                return str_repeat('*', $len);
            }, $text);
        }
        return $text;
    }

    protected function isStaffOrAdmin(): bool
    {
        $role = Auth::user()->role->name ?? '';
        return in_array($role, ['admin','nhanvien'], true);
    }

    /** ====== tiện ích lưu lịch sử vào file JSON ====== */
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
            'ts'   => $now,
            'by'   => ['id'=>Auth::id(), 'name'=>Auth::user()->name ?? ''],
            'rid'  => $review->id,
            'pid'  => $review->product_id,
        ];
        $data = [];
        if (file_exists($path)) {
            $json = @file_get_contents($path);
            $data = json_decode($json ?: '[]', true) ?: [];
        }
        $data[] = array_merge($base, $entry);
        @file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    }

    /** Gửi đánh giá gốc (user phải mua; admin/staff không bắt buộc) */
    public function store(Request $request, Product $product)
    {
        $user = Auth::user();

        if (method_exists($user, 'hasReviewedProduct') && $user->hasReviewedProduct($product->id)) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        if (!$this->isStaffOrAdmin()) {
            if (method_exists($user, 'hasPurchasedProduct') && !$user->hasPurchasedProduct($product->id)) {
                return back()->with('error', 'Bạn cần mua sản phẩm để đánh giá.');
            }
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
            'image'  => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        ProductReview::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'rating'     => (int) $request->rating,
            'review'     => $this->censor(trim($request->review)),
            'image'      => $imagePath,
            'status'     => 'approved', // ✅ hiển thị ngay
        ]);

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá!');
    }

    /** Trả lời một đánh giá (Admin/Staff luôn được; User được nếu đã mua sản phẩm) */
    public function reply(Request $request, ProductReview $review)
    {
        $user = Auth::user();
        $canReply = $this->isStaffOrAdmin();
        if (!$canReply && method_exists($user, 'hasPurchasedProduct')) {
            $canReply = $user->hasPurchasedProduct($review->product_id);
        }
        if (!$canReply) {
            return back()->with('error','Bạn cần mua sản phẩm để trả lời đánh giá này.');
        }

        $request->validate([
            'content' => 'required|string|min:2|max:2000',
        ]);

        $content = '[reply:#'.$review->id.'] ' . $this->censor(trim($request->content));

        ProductReview::create([
            'user_id'    => $user->id,
            'product_id' => $review->product_id,
            'rating'     => 0,
            'review'     => $content,
            'image'      => null,
            'status'     => 'approved',
        ]);

        return back()->with('success','Đã gửi phản hồi.');
    }

    /** Sửa đánh giá hoặc phản hồi (chủ sở hữu hoặc admin/staff) */
    public function update(Request $request, ProductReview $review)
    {
        $user = Auth::user();
        $can = ($review->user_id === $user->id) || $this->isStaffOrAdmin();
        if (!$can) return back()->with('error','Bạn không có quyền sửa.');

        $isReply = preg_match('/^\[reply:#\d+\]\s*/u', $review->review) === 1;

        $rules = ['content' => 'required|string|min:2|max:2000'];
        if (!$isReply) { $rules['rating'] = 'nullable|integer|min:1|max:5'; }
        $data = $request->validate($rules);

        $old = ['rating'=>$review->rating, 'review'=>$review->review];

        $clean = $this->censor(trim($data['content']));
        if ($isReply) {
            $prefix = '';
            if (preg_match('/^\[reply:#\d+\]\s*/u', $review->review, $m)) {
                $prefix = $m[0];
            }
            $payload = ['review' => $prefix . $clean];
        } else {
            $payload = ['review' => $clean];
            if (isset($data['rating'])) $payload['rating'] = (int) $data['rating'];
        }

        $review->update($payload);

        $this->appendHistory($review, [
            'action' => 'update',
            'old'    => $old,
            'new'    => ['rating'=>$review->rating, 'review'=>$review->review],
        ]);

        return back()->with('success','Đã cập nhật.');
    }

    /** Xoá đánh giá/ phản hồi (chủ sở hữu hoặc admin/staff) */
    public function destroy(ProductReview $review)
    {
        $user = Auth::user();
        $can = ($review->user_id === $user->id) || $this->isStaffOrAdmin();
        if (!$can) return back()->with('error','Bạn không có quyền xoá.');

        // Nếu xoá review gốc, xoá luôn các reply theo tiền tố
        if (preg_match('/^\[reply:#\d+\]/u', $review->review) !== 1) {
            ProductReview::where('product_id', $review->product_id)
                ->where('status','approved')
                ->where('review', 'like', '[reply:#'.$review->id.']%')
                ->delete();
        }

        if ($review->image) {
            Storage::disk('public')->delete($review->image);
        }

        $this->appendHistory($review, [
            'action' => 'delete',
            'old'    => ['rating'=>$review->rating, 'review'=>$review->review],
        ]);

        $review->delete();

        return back()->with('success','Đã xoá.');
    }
}
