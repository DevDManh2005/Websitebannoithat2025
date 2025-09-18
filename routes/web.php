<?php

use Illuminate\Support\Facades\Route;

// --- AUTH CONTROLLERS ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;

// --- FRONTEND CONTROLLERS ---
use App\Http\Controllers\Frontend\{
    AddressController,
    CartController,
    CategoryController,
    CheckoutController,
    OrderController,
    PaymentController,
    ProductController,
    ProfileController,
    ShippingController,
    WishlistController,
    ProductReviewController,
    VoucherController,
    HomeController,
    SearchController,
    BlogController as FrontBlogController,
    BlogCommentController,
    BlogLikeController,
    SupportTicketController
};

// --- ADMIN CONTROLLERS (tái sử dụng cho STAFF) ---
use App\Http\Controllers\Admin\{
    AdminController,
    BrandController,
    CategoryController as AdminCategoryController,
    InventoryController,
    OrderController as AdminOrderController,
    ProductController as AdminProductController,
    StaffController,
    SupplierController,
    UserController,
    ProductReviewController as AdminProductReviewController,
    VoucherController as AdminVoucherController,
    SettingController,
    SlideController,
    BlogCategoryController as AdminBlogCategoryController,
    BlogController as AdminBlogController,
    UploadController,
    PermissionController,
    RoutePermissionController,
    SupportTicketController as AdminSupportTicketController,
    SupportReplyController as AdminSupportReplyController,
    ReportController
};

/*
|--------------------------------------------------------------------------
| PUBLIC  nè nha
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/gioi-thieu', [PageController::class, 'about'])->name('about');
Route::get('/dieu-khoan-dich-vu', [PageController::class, 'terms'])->name('terms.show');
Route::get('/lien-he', [PageController::class, 'contact'])->name('contact');
Route::get('/chinh-sach-bao-hanh', [PageController::class, 'warranty'])->name('warranty.show');
Route::get('/giao-hang-doi-tra', [PageController::class, 'shippingReturns'])->name('shipping_returns.show');
Route::get('/cau-hoi-thuong-gap', [PageController::class, 'faq'])->name('faq.show'); // <-- THÊM DÒNG NÀY




Route::get('/danh-muc/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');

Route::get('/address/provinces', [AddressController::class, 'getProvinces'])->name('address.provinces');
Route::get('/address/districts', [AddressController::class, 'getDistricts'])->name('address.districts');
Route::get('/address/wards', [AddressController::class, 'getWards'])->name('address.wards');

Route::get('/register', fn() => view('auth.register'))->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/verify', fn() => view('auth.verify'))->name('verify.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('verify.otp');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend.otp');
Route::get('/login', fn() => view('auth.login'))->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot', fn() => view('auth.forgot'))->name('forgot.form');
Route::post('/forgot', [AuthController::class, 'forgot'])->name('forgot');
Route::get('/reset', fn() => view('auth.reset'))->name('reset.form');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');

Route::get('/payment/vnpay-return', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
Route::match(['GET', 'POST'], '/payment/vnpay-ipn', [PaymentController::class, 'vnpayIpn'])->name('payment.vnpay.ipn');

Route::get('/search', [SearchController::class, 'index'])->name('search');

/* BLOG PUBLIC (prefix /bai-viet) */
Route::prefix('bai-viet')->name('blog.')->group(function () {
    Route::get('/', [FrontBlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [FrontBlogController::class, 'show'])->name('show');
    Route::post('/bai-viet/{blog}/like', [BlogLikeController::class, 'toggle'])->name('blog.like.toggle');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED (USER)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/tai-khoan/cap-nhat', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/tai-khoan/doi-mat-khau', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    Route::get('/danh-sach-yeu-thich', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
    Route::post('/gio-hang/mua-ngay', [CartController::class, 'buyNow'])->name('cart.buyNow');
    Route::patch('/gio-hang/cap-nhat/{cartId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/gio-hang/xoa/{cartId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/gio-hang/toggle-select', [CartController::class, 'toggleSelect'])->name('cart.toggleSelect');
    Route::delete('/gio-hang/xoa-muc-chon', [CartController::class, 'removeSelected'])->name('cart.removeSelected');

    Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/dat-hang', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');

    Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/don-hang/{order}/huy', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/don-hang/{order}/da-nhan', [OrderController::class, 'receive'])->name('orders.receive');
    Route::patch('/don-hang/{order}/cap-nhat-dia-chi', [OrderController::class, 'updateAddress'])->name('orders.update-address');
    // web.php (nhóm auth)
    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/{review}/reply', [ProductReviewController::class, 'reply'])->name('reviews.reply');
    Route::patch('/reviews/{review}', [ProductReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ProductReviewController::class, 'destroy'])->name('reviews.destroy');


    Route::get('/payment/vnpay/create/{order}', [PaymentController::class, 'createVnpayPayment'])->name('payment.vnpay.create');
    Route::post('/shipping/fee', [ShippingController::class, 'getFee'])->name('shipping.getFee');
    Route::post('/voucher/apply', [VoucherController::class, 'apply'])->name('voucher.apply');
    Route::post('/voucher/remove', [VoucherController::class, 'remove'])->name('voucher.remove');

    /* BLOG AUTH (comments, like) */
    Route::prefix('bai-viet')->name('blog.')->group(function () {
        Route::post('/{blog}/comments', [BlogCommentController::class, 'store'])->name('comments.store');
        Route::post('/{blog}/like', [BlogLikeController::class, 'toggle'])->name('like.toggle');
    });

    /* SUPPORT (USER) */
    Route::get('/ho-tro', [SupportTicketController::class, 'index'])->name('support.index');
    Route::get('/ho-tro/tao', [SupportTicketController::class, 'create'])->name('support.create');
    Route::post('/ho-tro', [SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/ho-tro/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/ho-tro/{ticket}/rep', [SupportTicketController::class, 'reply'])->name('support.reply');
});

/*
|--------------------------------------------------------------------------
| ADMIN (chỉ admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->post('/uploads/ckeditor', [UploadController::class, 'ckeditor'])
    ->name('uploads.ckeditor');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::patch('/admin/slides/{slide}/toggle', [SlideController::class, 'toggle'])
            ->name('admin.slides.toggle')
            ->middleware(['auth', 'role:admin']);

        Route::get(
            '/dashboard',
            [\App\Http\Controllers\Admin\DashboardController::class, 'index']
        )->name('dashboard');
        Route::get('/admin/products/{productId}/variants-inventory', [InventoryController::class, 'variantsInventory'])
            ->name('admin.inventories.variants');
        // staff/admin mới vào được (nếu bạn có middleware riêng thì gắn thêm ở đây)
        Route::get('/reports', [ReportController::class, 'dashboard'])->name('reports.dashboard');
        // CSV export nhanh (không cần package)
        Route::get('/reports/export/top-products', [ReportController::class, 'exportTopProductsCsv'])->name('reports.export.top_products');
        // Nhân sự
        Route::resource('staffs', StaffController::class)->except(['show']);
        Route::resource('admins', AdminController::class)->except(['show']);

        // Người dùng
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::post('/{user}/toggle', [UserController::class, 'toggleLock'])->name('toggle');
        });

        // Đơn hàng
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::patch('/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
            Route::patch('/{order}/update-shipping', [AdminOrderController::class, 'updateShippingInfo'])->name('updateShippingInfo');
            Route::post('/{order}/ready-to-ship', [AdminOrderController::class, 'readyToShip'])->name('ready-to-ship');
            Route::patch('/{order}/cod-paid', [AdminOrderController::class, 'markCodPaid'])->name('cod-paid');
        });
        Route::get('/notifications/new-orders', [\App\Http\Controllers\Admin\OrderController::class, 'fetchNewOrders'])->name('notifications.new'); // <-- THÊM DÒNG NÀY

        // Đánh giá SP
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [AdminProductReviewController::class, 'index'])->name('index');
            Route::post('/{review}/toggle-status', [AdminProductReviewController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{review}', [AdminProductReviewController::class, 'destroy'])->name('destroy');
        });

        // Danh mục, thương hiệu, NCC, SP, kho, voucher, cài đặt, slide
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('products', AdminProductController::class);
        Route::get('/products/{product}/variants-inventory', [AdminProductController::class, 'getVariantsWithInventory'])
            ->name('products.variants.inventory');
        Route::resource('inventories', InventoryController::class);
        Route::resource('vouchers', AdminVoucherController::class);

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::resource('slides', SlideController::class);

        // Blog admin
        Route::resource('blog-categories', AdminBlogCategoryController::class)->except(['show']);
        Route::resource('blogs', AdminBlogController::class)->except(['show']);

        // ===== Support (ADMIN) =====
        Route::prefix('support-tickets')->name('support_tickets.')->group(function () {
            Route::get('/', [AdminSupportTicketController::class, 'index'])->name('index');
            Route::get('/{support_ticket}', [AdminSupportTicketController::class, 'show'])->name('show');
            Route::patch('/{support_ticket}/status', [AdminSupportTicketController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{support_ticket}/replies', [AdminSupportReplyController::class, 'store'])->name('replies.store');
        });

        // Upload + quản trị quyền
        Route::post('/uploads/ckeditor', [UploadController::class, 'ckeditor'])->name('uploads.ckeditor');

        Route::resource('permissions', PermissionController::class)->except(['show']);
        Route::resource('route-permissions', RoutePermissionController::class)->except(['show']);

        // Banners / Pages (nếu có)
        if (class_exists('App\Http\Controllers\Admin\BannerController')) {
            Route::resource('banners', 'App\Http\Controllers\Admin\BannerController');
        }
        if (class_exists('App\Http\Controllers\Admin\PageController')) {
            Route::resource('pages', 'App\Http\Controllers\Admin\PageController');
        }
    });

/*
|--------------------------------------------------------------------------
| STAFF (staff hoặc admin vào được)
|--------------------------------------------------------------------------
| Dùng middleware 'auto.permission' để tự kiểm tra quyền theo bảng route_permissions
*/
Route::prefix('staff')
    ->name('staff.')
    ->middleware(['auth', 'role:staff,admin', 'auto.permission'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Staff\DashboardController::class, 'index'])
            ->name('dashboard');
        /* ========== KINH DOANH ========== */
        Route::get('/notifications/new-orders', [\App\Http\Controllers\Admin\OrderController::class, 'fetchNewOrders'])->name('notifications.new'); // <-- THÊM DÒNG NÀY
        // Orders (tái dùng Admin\OrderController) — giữ camelCase cho readyToShip/codPaid
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::patch('/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
            Route::patch('/{order}/update-shipping', [AdminOrderController::class, 'updateShippingInfo'])->name('updateShippingInfo');
            Route::post('/{order}/ready-to-ship', [AdminOrderController::class, 'readyToShip'])->name('readyToShip');
            Route::patch('/{order}/cod-paid', [AdminOrderController::class, 'markCodPaid'])->name('codPaid');
        });

        // Products / Variants inventory
        Route::resource('products', AdminProductController::class);
        Route::get('/products/{product}/variants-inventory', [AdminProductController::class, 'getVariantsWithInventory'])
            ->name('products.variants.inventory');

        // Categories / Brands / Suppliers / Inventories / Vouchers
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('inventories', InventoryController::class);
        Route::resource('vouchers', AdminVoucherController::class);

        // Reviews
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [AdminProductReviewController::class, 'index'])->name('index');
            Route::post('/{review}/toggle-status', [AdminProductReviewController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{review}', [AdminProductReviewController::class, 'destroy'])->name('destroy');
        });

        /* ========== NỘI DUNG / MEDIA ========== */
        Route::resource('slides', SlideController::class);
        Route::resource('blog-categories', AdminBlogCategoryController::class)->except(['show']);
        Route::resource('blogs', AdminBlogController::class)->except(['show']);
        // Banners (nếu có controller)
        if (class_exists('App\Http\Controllers\Admin\BannerController')) {
            Route::resource('banners', 'App\Http\Controllers\Admin\BannerController');
        }
        // Pages (nếu có controller)
        if (class_exists('App\Http\Controllers\Admin\PageController')) {
            Route::resource('pages', 'App\Http\Controllers\Admin\PageController');
        }

        /* ========== HỆ THỐNG ========== */
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::post('/{user}/toggle', [UserController::class, 'toggleLock'])->name('toggle');
        });

        Route::resource('staffs', StaffController::class)->except(['show']);
        Route::resource('admins', AdminController::class)->except(['show']);

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::resource('permissions', PermissionController::class)->except(['show']);
        Route::resource('route-permissions', RoutePermissionController::class)->except(['show']);
        Route::prefix('support-tickets')->name('support_tickets.')->group(function () {
            Route::get('/', [AdminSupportTicketController::class, 'index'])->name('index');
            Route::get('/{support_ticket}', [AdminSupportTicketController::class, 'show'])->name('show');
            Route::patch('/{support_ticket}/status', [AdminSupportTicketController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{support_ticket}/replies', [AdminSupportReplyController::class, 'store'])->name('replies.store');
        });
    });
