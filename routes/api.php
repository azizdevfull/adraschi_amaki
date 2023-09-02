<?php

use App\Http\Controllers\Api\Mobile\Admin\AdminProductsController;
use App\Http\Controllers\Api\Mobile\Admin\NotificationController;
use App\Http\Controllers\Api\Mobile\ClickController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\HomeController;
use App\Http\Controllers\Api\Mobile\PaymentController;
use App\Http\Controllers\Api\Mobile\ProductController;
use App\Http\Controllers\Api\Mobile\ProfileController;
use App\Http\Controllers\Api\Mobile\Admin\ReklamaController;
use App\Http\Controllers\Api\Mobile\Admin\CategoryController;
use App\Http\Controllers\Api\Mobile\Admin\PaymentSecretController;
use App\Http\Controllers\Api\Mobile\Admin\AdminUserCategoryController;
use App\Http\Controllers\Api\Mobile\Admin\AdminUsersController;
use App\Http\Controllers\Api\Mobile\Admin\RegionController;
use App\Http\Controllers\Api\Mobile\GhostViewsController;
use App\Http\Controllers\Api\Mobile\IshlabChiqarishController;
use App\Http\Controllers\Api\Mobile\LikeController;
use App\Http\Controllers\Api\Mobile\MahsulotTolaController;
use App\Http\Controllers\Api\Mobile\OrderController;
use App\Http\Controllers\Api\Mobile\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Mobile\ProductSearchController;
use App\Http\Controllers\Api\Mobile\UserCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware('localization')->prefix('mobile')->group(function () {

    // Ghost routes
    Route::get('/last-views', [GhostViewsController::class, 'index']);
    Route::post('/payment/prepare', [ClickController::class, 'prepare']);
    Route::post('/payment/complete', [ClickController::class, 'complete']);
    // Home Routes
    Route::get('/home', [HomeController::class, 'home']);
    Route::get('/ishlab-chiqarishlar', [IshlabChiqarishController::class, 'index']);
    Route::get('/mahsulot-tolalari', [MahsulotTolaController::class, 'index']);
    Route::get('/categories', [UserCategoryController::class, 'index']);
    Route::get('/product-categories', [UserCategoryController::class, 'productCategories']);
    Route::get('/categories/{category}', [UserCategoryController::class, 'showCategory']);
    Route::get('/regions', [RegionController::class, 'index']);
    Route::get('/regions/{region}', [RegionController::class, 'showRegion']);
    Route::get('/users-categories', [UserCategoryController::class, 'usercategories']);
    Route::get('/reklama', [ReklamaController::class, 'index']);

    // Like Routes
    Route::post('/products/{id}/like', [LikeController::class, 'likePost']);
    Route::delete('/products/{id}/unlike', [LikeController::class, 'unlikePost']);

    // Full Auth Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logoutUser'])->middleware('auth:sanctum');
    Route::post('/delete-account', [AuthController::class, 'sendSmsDeleteAccount']);
    Route::post('/confirm-delete-sms', [AuthController::class, 'deleteAccount']);
    Route::post('/verify', [AuthController::class, 'verifySms']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/resend-code', [AuthController::class, 'resendSms']);

    // Search Routes
    Route::get('/search', [ProductSearchController::class, 'index']);

    // Products Routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    // Reklama Routes
    Route::apiResource('reklama', ReklamaController::class);
    Route::post('reklama/{reklama}', [ReklamaController::class, 'update']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/create-orders', [OrderController::class, 'store']);
        Route::get('/orders', [OrderController::class, 'index']);

        Route::post('/products', [ProductController::class, 'store']);
        Route::post('/pay', [PaymentController::class, 'pay']);
        Route::post('/products/{product}', [ProductController::class, 'update']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::patch('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        Route::post('/products/{product}/favorite', [ProductController::class, 'toggleFavorite']);
        Route::delete('/products/{product}/favorite', [ProductController::class, 'removeFavorite']);

        Route::get('/users/favorites', [ProfileController::class, 'favourites']);
    });

    // Profile Routes
    Route::get('/profile/{user}', [ProfileController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [ProfileController::class, 'Profile']);
        Route::post('/profile-update', [ProfileController::class, 'ProfileUpdate']);
    });

    // Admin Routes
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {

        // Orders Routes
        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{id}', [AdminOrderController::class, 'show']);

        // Notification Routes
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/read-notifications', [NotificationController::class, 'readNotifications']);
        Route::get('/unread-notifications', [NotificationController::class, 'unReadNotifications']);
        Route::get('/notifications/{id}/read', [NotificationController::class, 'markRead']);

        Route::post('/products', [ProductController::class, 'store']);
        Route::post('/pay', [PaymentController::class, 'pay']);
        Route::post('/products/{product}', [ProductController::class, 'update']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::patch('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        Route::post('/products/{product}/favorite', [ProductController::class, 'toggleFavorite']);
        Route::delete('/products/{product}/favorite', [ProductController::class, 'removeFavorite']);
        // Route::apiResource('users-categories', AdminUserCategoryController::class);
        // Route::apiResource('categories', CategoryController::class);
        // Route::apiResource('regions', RegionController::class);
        // Route::post('products/{products}', [AdminProductsController::class, 'update']);
        // Route::apiResource('products', AdminProductsController::class);
        // Route::post('users/{user}', [AdminUsersController::class, 'update']);
        // Route::apiResource('users', AdminUsersController::class);
        // // Route::apiResource('reklama', ReklamaController::class);
        // Route::post('reklama/{reklama}', [ReklamaController::class, 'update']);
        // Route::apiResource('payment-secrets', PaymentSecretController::class);
    });
});
