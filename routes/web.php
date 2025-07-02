<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatApiController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuccessController;
use App\Http\Controllers\LangController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderPhaseController;
use App\Http\Controllers\PaymentMethod;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceOfferController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamUserController;
use App\Http\Controllers\WalletController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('lang/{locale}', [LangController::class, 'setLocale'])->name('setLocale');
Route::get('/success/{id}/{offer_id}', [SuccessController::class, 'index'])->name('success');
Route::get('/charge_in/{id}', [SuccessController::class, 'charge_in'])->name('charge_in');
Route::get('/', function () {
    return redirect()->route('home');
});
// routes/web.php

Route::get('/outh', function () {
    return view('outh');
});
Auth::routes();
Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::resource('roles', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('users', UserController::class);
    Route::get('sellers_list', [UserController::class,'sellers_list'])->name('sellers_list');
    Route::get('sellers_active/{id}', [UserController::class,'sellers_active'])->name('sellers_active');
    Route::get('sellers_inactive/{id}', [UserController::class,'sellers_inactive'])->name('sellers_inactive');
    Route::resource('category', CategoryController::class);
    Route::resource('request', RequestController::class);
    Route::resource('product', ProductController::class);
    Route::post('category/add_favourit', 'CategoryController@add_favourit');
    Route::resource('notifications', NotificationController::class);

    Route::resource('wallet', WalletController::class);
    Route::resource('banners', BannerController::class);

    Route::get('banner_active/{id}', [BannerController::class,'banner_active'])->name('banner_active');
    Route::get('banner_inactive/{id}', [BannerController::class,'banner_inactive'])->name('banner_inactive');

    Route::resource('orders', OrderController::class);
    Route::resource('payment_method', PaymentMethod::class);
    Route::get('active/{id}', [PaymentMethod::class,'active'])->name('active');
    Route::get('inactive/{id}', [PaymentMethod::class,'inactive'])->name('inactive');

    // Service routes
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services/store', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');

    Route::delete('services/{service}/thumbnail', [ServiceController::class, 'deleteThumbnail'])->name('services.deleteThumbnail');
    Route::delete('services/{service}/images/{image}', [ServiceController::class, 'deleteImage'])->name('services.deleteImage');
    Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');


    Route::resource('teams', TeamController::class);

    Route::resource('team_users', TeamUserController::class);
    Route::resource('customers', CustomerController::class);

    Route::resource('articles', ArticleController::class);


    // Display the service order creation form
    
    Route::resource('service_orders', ServiceOrderController::class);

    // Handle AJAX request to fetch service data
    Route::get('/fetch-service-data', [ServiceOrderController::class, 'fetchServiceData'])->name('fetch-service-data');

    // Store the service order
    Route::post('/service_order', [ServiceOrderController::class, 'store'])->name('service_order.store');

    Route::post('/service_order/store', [ServiceOrderController::class, 'store'])->name('service_order.store');

    Route::post('/articles/delete-image', [ArticleController::class, 'deleteImage'])->name('articles.deleteImage');
    Route::resource('service_offers', ServiceOfferController::class);
    Route::post('/service_offers/{id}/delete-image', [ServiceOfferController::class, 'deleteImage'])->name('service_offers.delete_image');
    Route::get('/team/{id}/users', [ServiceOrderController::class, 'getTeamUsers'])->name('team.users');
    Route::post('/service-order/update', [ServiceOrderController::class, 'updateOrder'])->name('service-order.update');
    Route::post('/order-phase/{id}/update-status', [OrderPhaseController::class, 'updateStatus'])->name('order-phase.update-status');
    Route::patch('/service-order/{id}/update-status', [ServiceOrderController::class, 'updateStatus'])->name('service-order.update-status');

    Route::get('/chats', [ChatApiController::class, 'index'])->name('chats.index');
    Route::get('/chats/{customerId}', [ChatApiController::class, 'show'])->name('chats.show');
    Route::post('/chats', [ChatApiController::class, 'store'])->name('chats.store');
    Route::get('/wallet/{id}/history', [WalletController::class, 'history'])->name('wallet.history');

});


