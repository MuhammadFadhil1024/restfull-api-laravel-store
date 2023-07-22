<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\TransactionHisstoryController;
use App\Http\Controllers\UserController;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('/v1')->group(function () {

    Route::prefix('/users')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/', [UserController::class, 'update'])->middleware('auth:api');
        Route::put('/topup', [UserController::class, 'topup'])->middleware('auth:api');
    });

    Route::middleware(['auth:api', 'IsAdmin'])->prefix('/categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'delete']);
    });

    Route::middleware(['auth:api'])->prefix('/products')->group( function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store'])->middleware('IsAdmin');
        Route::put('/{id}', [ProductController::class, 'update'])->middleware('IsAdmin');
        Route::patch('/{id}', [ProductController::class, 'updateCategory'])->middleware('IsAdmin');
        Route::delete('/{id}', [ProductController::class, 'delete'])->middleware('IsAdmin');
    });

    Route::middleware(['auth:api'])->prefix('/transactions')->group(function () {
        Route::post('/', [TransactionHisstoryController::class, 'store']);
        Route::get('/users', [TransactionHisstoryController::class, 'transactionUser']);
        Route::get('/admin', [TransactionHisstoryController::class, 'transactionAdmin'])->middleware('IsAdmin');
        Route::get('/{id}', [TransactionHisstoryController::class, 'transactionDetail'])->middleware('transaction');
    });

});
