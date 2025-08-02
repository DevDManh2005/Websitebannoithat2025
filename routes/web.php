<?php

use Illuminate\Support\Facades\Route;

// --- AUTHENTICATION ---
use App\Http\Controllers\AuthController;

// --- FRONTEND CONTROLLERS ---
use App\Http\Controllers\Frontend\AddressController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\ShippingController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\ProductReviewController;
use App\Http\Controllers\Frontend\VoucherController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\SearchController;
// --- ADMIN CONTROLLERS ---
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductReviewController as AdminProductReviewController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SlideController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Guest accessible)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
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
Route::get('/payment/vnpay-callback', [PaymentController::class, 'vnpayCallback'])->name('payment.vnpayCallback');
Route::get('/search', [SearchController::class, 'index'])->name('search');




/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (User must be logged in)
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
    Route::patch('/gio-hang/cap-nhat/{cartId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/gio-hang/xoa/{cartId}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/dat-hang', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
    Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/don-hang/{order}/huy', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/don-hang/{order}/da-nhan', [OrderController::class, 'markAsReceived'])->name('orders.markAsReceived'); // <-- Sửa lại tên phương thức

    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('reviews.store');

    Route::get('/payment/vnpay/create/{order}', [PaymentController::class, 'createVnpayPayment'])->name('payment.vnpay.create');

    Route::post('/shipping/fee', [ShippingController::class, 'getFee'])->name('shipping.getFee');
    Route::post('/voucher/apply', [VoucherController::class, 'apply'])->name('voucher.apply');
    Route::post('/voucher/remove', [VoucherController::class, 'remove'])->name('voucher.remove');
});


/*
|--------------------------------------------------------------------------
| ADMIN & STAFF ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admins.dashboard'))->name('dashboard');

    Route::resource('staffs', StaffController::class)->except(['show']);
    Route::resource('admins', AdminController::class)->except(['show']);

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::post('/{user}/toggle', [UserController::class, 'toggleLock'])->name('toggle');
        Route::get('/{user}/logs', [UserController::class, 'logs'])->name('logs');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
        Route::patch('/{order}/update-shipping', [AdminOrderController::class, 'updateShippingInfo'])->name('updateShippingInfo');

        // Đổi tên route cho đúng với GHN
        Route::post('/{order}/create-ghn', [AdminOrderController::class, 'createGhnOrder'])->name('create-ghn');
        Route::post('/{order}/cancel-ghn', [AdminOrderController::class, 'cancelGhnOrder'])->name('cancel-ghn');
    });

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminProductReviewController::class, 'index'])->name('index');
        Route::post('/{review}/toggle-status', [AdminProductReviewController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{review}', [AdminProductReviewController::class, 'destroy'])->name('destroy');
    });
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', AdminProductController::class);
    Route::resource('inventories', InventoryController::class);
    Route::resource('vouchers', AdminVoucherController::class);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('slides', SlideController::class);
});

// Staff Routes
Route::middleware(['auth', 'role:staff'])->get('/staff/dashboard', fn() => view('staffs.dashboard'))->name('staff.dashboard');
