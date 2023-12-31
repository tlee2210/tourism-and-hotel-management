<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\UserController;
use App\Http\Controllers\dashboard\PlacesController;
use App\Http\Controllers\dashboard\hotelController;
use App\Http\Controllers\dashboard\roomController;
use App\Http\Controllers\dashboard\tourController;
use App\Http\Controllers\dashboard\tourTimeController;
use App\Http\Controllers\dashboard\postcontroller;
use App\Http\Controllers\dashboard\categoryController;
use App\Http\Controllers\dashboard\bedtypeController;
use App\Http\Controllers\dashboard\vehicleController;
use App\Http\Controllers\dashboard\BookingHotelController as bookinghotel;
use App\Http\Controllers\dashboard\bookingtourController as bookingtour;
use App\Http\Controllers\home\AuthController;
use App\Http\Controllers\home\homeTourController;
use App\Http\Controllers\home\ProfileController;
use App\Http\Controllers\home\homeHotelController;
use App\Http\Controllers\home\bookingtourController;
use App\Http\Controllers\home\bookinghotelController;
use App\Http\Controllers\home\paymentController;
use App\Http\Controllers\PayPalController;

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


Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('/register', [AuthController::class, 'processRegistration']);
Route::post('/forgetPassword', [AuthController::class, 'forgetPassword']);
Route::get('/change-Password/{user}/{token}', [AuthController::class, 'showResetPasswordForm'])->name('resetPassword');
Route::post('/change-Password/{user}/{token}', [AuthController::class, 'processResetPassword'])->name('resetPassword.process');

Route::prefix('dashboard')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/search', [UserController::class, 'search']);
        Route::get('/create', [UserController::class, 'create']);
        Route::post('/create', [UserController::class, 'store']);
        Route::get('/{id}/edit', [UserController::class, 'edit']);
        Route::post('/{id}', [UserController::class, 'update']);
    });

    Route::prefix('places')->group(function () {
        Route::get('/', [PlacesController::class, 'index']);
        Route::post('/search', [PlacesController::class, 'search']);
        Route::get('/create', [PlacesController::class, 'create']);
        Route::post('/create', [PlacesController::class, 'store']);
        Route::get('/{slug}/edit', [PlacesController::class, 'edit']);
        Route::post('/{slug}', [PlacesController::class, 'update']);
        Route::get('/prominent/{id}', [PlacesController::class, 'prominent']);
        Route::post('/delete/{id}', [PlacesController::class, 'delete']);
    });

    Route::prefix('Hotel')->group(function () {
        Route::get('/create', [hotelController::class, 'createHotel']);
        Route::post('/create', [hotelController::class, 'storeHotel']);

        Route::prefix('amenitie')->group(function () {
            Route::get('/', [roomController::class, 'getamenitie']);
            Route::post('/search', [roomController::class, 'searchamenitie']);
            Route::post('/create', [roomController::class, 'storeamenitie']);
            Route::post('/delete/{id}', [roomController::class, 'deleteamenitie']);
            Route::get('/{id}/edit', [roomController::class, 'edit']);
            Route::post('/{id}/edit', [roomController::class, 'update']);
        });

        Route::prefix('bedtype')->group(function () {
            Route::get('/search', [bedtypeController::class, 'search']);
            Route::get('/', [bedtypeController::class, 'getbedtype']);
            Route::post('/create', [bedtypeController::class, 'storebedtype']);
            Route::post('/delete/{id}', [bedtypeController::class, 'delete']);
            Route::get('/{id}/edit', [bedtypeController::class, 'edit']);
            Route::post('/{id}/edit', [bedtypeController::class, 'update']);
        });

        Route::get('/', [hotelController::class, 'index']);
        Route::post('/search', [hotelController::class, 'search']);
        Route::get('/{slug}/edit', [hotelController::class, 'editHotel']);
        Route::post('/{slug}/edit', [hotelController::class, 'updateHotel']);
        Route::post('/delete/{id}', [hotelController::class, 'deleteHotel']);

        Route::get('/{slug}/check', [roomController::class, 'checkRoomNumber']);
        Route::get('/{slug}/room', [roomController::class, 'getRoom']);
        Route::post('/{slug}/room/search', [roomController::class, 'search']);
        Route::get('/{slug}/room/create', [roomController::class, 'getCreateRoom']);
        Route::post('/{slug}/room/create', [roomController::class, 'storeRoom']);
        Route::get('/{slug}/room/{slugRoom}/edit', [roomController::class, 'getEditRoom']);
        Route::post('/{slug}/room/{slugRoom}/edit', [roomController::class, 'updateRoom']);
        Route::post('/{slug}/room/delete/{id}', [roomController::class, 'deleteRoom']);
    });

    Route::prefix('tour')->group(function () {
        Route::get('/', [tourController::class, 'getTours']);
        Route::post('/search', [tourController::class, 'search']);
        Route::get('/create', [tourController::class, 'getCreateTour']);
        Route::post('/create', [tourController::class, 'storeTour']);
        Route::get('/{slug}/edit', [tourController::class, 'getEditTour']);
        Route::post('/{slug}/edit', [tourController::class, 'updateTour']);
        Route::post('/delete/{id}', [tourController::class, 'delete']);
        Route::get('/prominent/{id}', [tourController::class, 'prominent']);

        Route::get('/{slug}/time', [tourTimeController::class, 'getTourtime']);
        Route::post('/{slug}/time/search', [tourTimeController::class, 'search']);
        Route::get('/{slug}/time/create', [tourTimeController::class, 'getCreateTourTime']);
        Route::post('/{slug}/time/create', [tourTimeController::class, 'storeTourTimes']);
        Route::get('/{slug}/time/{id}/edit', [tourTimeController::class, 'getEditTourTime']);
        Route::post('/{slug}/time/{id}/edit', [tourTimeController::class, 'updateTourTime']);
        Route::post('/{slug}/time/delete/{id}', [tourTimeController::class, 'deleteTourTime']);
    });

    Route::prefix('post')->group(function () {
        Route::get('/', [postcontroller::class, 'index']);
        Route::post('/search', [postcontroller::class, 'search']);
        Route::get('/create', [postcontroller::class, 'getCreate']);
        Route::post('/create', [postcontroller::class, 'store']);
        Route::get('/{slug}/edit', [postcontroller::class, 'getEdit']);
        Route::post('/{slug}/edit', [postcontroller::class, 'update']);
        Route::post('/delete/{id}', [postcontroller::class, 'delete']);
    });

    Route::prefix('vehicle')->group(function () {
        Route::get('/', [vehicleController::class, 'getvehicle']);
        Route::post('/create', [vehicleController::class, 'create']);
        Route::get('/{id}/edit', [vehicleController::class, 'edit']);
        Route::post('/{id}/edit', [vehicleController::class, 'update']);
        Route::post('/delete/{id}', [vehicleController::class, 'delete']);
        Route::get('/search', [vehicleController::class, 'search']);
    
    });

    Route::prefix('category')->group(function () {
        Route::get('/', [categoryController::class, 'getcategoty']);
        Route::post('/search', [categoryController::class, 'search']);
        Route::post('/create', [categoryController::class, 'createCategory']);
        Route::get('/{id}/edit', [categoryController::class, 'edit']);
        Route::post('/{id}/edit', [categoryController::class, 'update']);
        Route::post('/delete/{id}', [categoryController::class, 'delete']);
    });

    Route::prefix('bookingtour')->group(function () {
        Route::get('/', [bookingtour::class, 'getbooking']);
        Route::get('/confirm/{id}', [bookingtour::class, 'confirm']);
        //here
        Route::get('/search', [bookingtour::class, 'search']);
        Route::get('/checkdate/{id?}', [bookingtour::class, 'checktour']);
        Route::get('/abort/{id}', [bookingtour::class, 'abort']);
        Route::get('{code}/detail', [bookingtour::class, 'detailbooking']);
    });

    Route::prefix('bookinghotel')->group(function () {
        Route::get('/', [bookinghotel::class, 'getbooking']);
        Route::get('/{id}/detail', [bookinghotel::class, 'getdetail']);
    });
});

Route::post('/upload', [hotelController::class, 'upload']);

Route::prefix('hotel')->group(function () {
    Route::get('/home', [homeHotelController::class, 'indexHotel']);
    Route::post('/advancedsearch', [homeHotelController::class, 'advancedsearch']);
    Route::get('/{slug}', [homeHotelController::class, 'hoteldetail']);
    Route::get('/search/{search?}', [homeHotelController::class, 'searchHotel']);
});

Route::prefix('tour')->group(function () {
    Route::get('/search/{search?}', [homeTourController::class, 'searchTour']);
    Route::post('/advancedsearch', [homeTourController::class, 'advancedsearch']);
    Route::get('/', [homeTourController::class, 'show']);
    Route::get('/{slug}', [homeTourController::class, 'tourdetail']);
});
Route::prefix('profile')->middleware('auth:api')->group(function () {
    Route::get('/', [ProfileController::class, 'profile']);
    Route::post('/profileChange', [ProfileController::class, 'profileChange']);
    Route::get('/bookingtour', [ProfileController::class, 'getbookingtour']);
    Route::get('/bookinghotel', [ProfileController::class, 'getbookinghotel']);
    Route::post('/deletetour/{id}', [ProfileController::class, 'deletetour']);

    Route::post('/uploadAvatar', [ProfileController::class, 'uploadAvatar']);
});

Route::prefix('bookingtour')->middleware('auth:api')->group(function () {
    Route::get('/', [bookingtourController::class, 'getbooking']);
    Route::post('/addtocar', [bookingtourController::class, 'addtocart']);
    Route::post('/customerInformation', [bookingtourController::class, 'customerInformation']);
    Route::get('/checkout/{code}', [bookingtourController::class, 'checkout']);
    Route::post('/delete/{id}', [bookingtourController::class, 'delete']);
    Route::get('/booking/{id}/{adults}/{children}', [bookingtourController::class, 'booking']);
});
Route::prefix('bookinghotel')->middleware('auth:api')->group(function () {
    Route::get('/{slug}/{hotelid}', [bookinghotelController::class, 'getbooking']);
    Route::post('/checkInformation', [bookinghotelController::class, 'checkInformation']);
});

Route::prefix('paypal')->group(function () {
    Route::post('/payment/tour', [paymentController::class, 'paymenttour'])->name('payment.tour');
    Route::post('/payment/hotel', [paymentController::class, 'paymenthotel'])->name('payment.hotel');
    // Route::get('/payment/cancel/tour', [paymentController::class, 'paymentCancel'])->name('cancel.tour');
    Route::get('/payment/success/tour', [paymentController::class, 'paymenttourSuccess'])->name('success.tour');
    Route::get('/payment/success/hotel', [paymentController::class, 'paymenthotelSuccess'])->name('success.hotel');
});
