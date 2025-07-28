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


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Guest accessible)
|--------------------------------------------------------------------------
*/
// Home & Static Pages
Route::get('/', fn() => view('index'))->name('home');

// Products & Categories
Route::get('/danh-muc/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('product.show');

// Address API (GHTK)
Route::get('/address/provinces', [AddressController::class, 'getProvinces'])->name('address.provinces');
Route::get('/address/districts', [AddressController::class, 'getDistricts'])->name('address.districts');
Route::get('/address/wards', [AddressController::class, 'getWards'])->name('address.wards');

// Auth Routes
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

// Payment Callbacks
Route::get('/payment/vnpay-callback', [PaymentController::class, 'vnpayCallback'])->name('payment.vnpayCallback');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (User must be logged in)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/tai-khoan', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/tai-khoan/cap-nhat', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/tai-khoan/doi-mat-khau', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Wishlist
    Route::get('/danh-sach-yeu-thich', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Cart
    Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/gio-hang/cap-nhat/{cartId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/gio-hang/xoa/{cartId}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout & Orders
    Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/dat-hang', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
    Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // Shipping API (GHTK)
    Route::post('/shipping/fee', [ShippingController::class, 'getFee'])->name('shipping.getFee');
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

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::resource('categories', AdminCategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', AdminProductController::class);
    Route::resource('inventories', InventoryController::class);
});

Route::middleware(['auth', 'role:staff'])
    ->get('/staff/dashboard', fn() => view('staffs.dashboard'))
    ->name('staff.dashboard');
