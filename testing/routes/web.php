<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\InvoiceController;

Route::middleware(['auth'])->group(function () {
    // Hiển thị form tạo hóa đơn
    Route::get('/invoices/create', [InvoiceController::class, 'create'])
        ->name('invoices.create');

    // Thêm sản phẩm vào giỏ hàng
    Route::post('/invoices/add-to-cart', [InvoiceController::class, 'addToCart'])
        ->name('invoices.addToCart');

    // Lưu hóa đơn
    Route::post('/invoices', [InvoiceController::class, 'store'])
        ->name('invoices.store');

    // Hiển thị danh sách hóa đơn
    Route::get('/invoices', [InvoiceController::class, 'index'])
        ->name('invoices.index');
});

Route::get('/invoices/{invoice}/details', [InvoiceController::class, 'details'])
    ->name('invoices.details');

use App\Http\Controllers\ProductController;

Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');