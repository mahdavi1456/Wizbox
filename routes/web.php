<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::resource('product', ProductController::class);
Route::resource('category', CategoryController::class);
Route::resource('transport', TransportController::class);
Route::resource('account', AccountController::class);

Route::prefix('account')->group(function () {
    Route::get('{accountId}/users', [UserController::class, 'showUsers'])->name('user.showUsers');
    Route::get('{accountId}/users/{userId}/edit', [UserController::class, 'editUser'])->name('user.editUser');
    Route::put('{accountId}/users/{userId}', [UserController::class, 'updateUser'])->name('users.updateUser');
    Route::delete('{accountId}/users/{userId}', [UserController::class, 'destroyUser'])->name('account.users.destroy');
});

Route::get('users/create/{accountId}', [UserController::class, 'create'])->name('users.create');
Route::post('users', [UserController::class, 'store'])->name('users.store');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/uploadfile', [ApiController::class, 'uploadfile']);

require __DIR__.'/auth.php';
