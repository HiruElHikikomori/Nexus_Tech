<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\userController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Users\CartItemsController;
use App\Http\Controllers\Users\AccountController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Users\ReviewsController;   // <-- ya importado
use App\Http\Controllers\Users\ReportsController;   // <-- ya importado

// Home
Route::get('/', [ProductController::class, "RandomProductOrder"])->name('index');

// Registro
Route::get('/register', [RegisterController::class, "ShowRegisterForm"])
    ->middleware('guest')
    ->name('register');
Route::post('/register', [RegisterController::class, "Register"])
    ->middleware('guest');

// Login
Route::get('/login', [LoginController::class, "ShowLoginForm"])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [LoginController::class, "Login"])
    ->middleware('guest');

// Logout
Route::post('/logout', [LoginController::class, "Logout"])->name('logout');

// About us
Route::get('/aboutus', fn () => view('aboutus'));

// ========================
// Zona Usuario (auth + rol)
// ========================
Route::middleware(['auth', 'role:Usuario'])->group(function () {
    // Catálogo usuario
    Route::get('/products', [ProductController::class, "ProductUser"])->name('user.product');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

    // Agregar al carrito (POST /products desde tarjeta de producto)
    Route::post('/products', [CartItemsController::class, "store"])->name('cart.add-item');

    // Carrito
    Route::resource('/UserCart', CartItemsController::class)->only(['index', 'destroy'])->parameters([
        'UserCart' => 'cartItem',
    ])->names([
        'index' => 'user.cart',
        'destroy' => 'delete.cart.item',
    ]);

    // Actualizar cantidad (AJAX)
    Route::patch('/cart/{cartItem}/update-quantity', [CartItemsController::class, "updateQuantity"])
        ->name('cart.update-item-quantity');

    // Vaciar carrito (si sigues usando este flujo)
    Route::delete('/UserCart', [CartItemsController::class, "deleteAll"])
        ->name('delete.cart.items');

    // ✅ Checkout (NUEVO): confirma/cancela compra -> crea orders/order_items
    Route::post('/checkout/confirm', [CartItemsController::class, 'confirmCheckout'])->name('checkout.confirm');
    Route::post('/checkout/cancel',  [CartItemsController::class, 'cancelCheckout'])->name('checkout.cancel');

    // ✅ Reviews (NUEVO)
    Route::post('/reviews', [ReviewsController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewsController::class, 'destroy'])->name('reviews.destroy');

    // ✅ Reports (NUEVO)
    Route::post('/reports', [ReportsController::class, 'store'])->name('reports.store');

    // Perfil usuario
    Route::get('/userProfile', [AccountController::class, "show"])->name('user.profile');
    Route::get('/users/{userId}/edit', [AccountController::class, 'edit'])->name('users.edit');      // <-- FIX slash
    Route::put('/users/{userId}', [AccountController::class, 'update'])->name('users.update');
});

// ========================
// Zona Admin
// ========================

// Panel
Route::get('/controlPanel', fn () => view('admin.controlPanel'))
    ->name('admin.controlPanel')
    ->middleware(['auth', 'role:Administrador']);

// Búsquedas (ajusta el controller correcto si no es AdminController)
Route::get('/AdminProducts/search', [ProductController::class, 'unifiedSearch'])
    ->name('admin.products.search')
    ->middleware(['auth', 'role:Administrador']);

// Productos (resource admin)
Route::resource('/AdminProducts', ProductController::class)->parameters([
    'AdminProducts' => 'product',
])->names([
    'index'   => 'admin.products.index',
    'store'   => 'admin.products.store',
    'show'    => 'admin.products.show',
    'update'  => 'admin.products.update',
    'destroy' => 'admin.products.destroy',
])->middleware(['auth', 'role:Administrador']);

// Perfil admin
Route::get('/adminProfile', [AdminAccountController::class, "show"])->name('admin.profile');
Route::get('/admin/{userId}/edit', [AdminAccountController::class, 'edit'])->name('admin.edit');    // <-- FIX slash
Route::put('/admin/users/{userId}', [AdminAccountController::class, 'update'])->name('admin.update');

// Usuarios (resource admin)
Route::resource('/AdminUsers', userController::class)->parameters([
    'AdminUsers' => 'user',
])->names([
    'index'   => 'admin.users.index',
    'show'    => 'admin.users.show',
    'destroy' => 'admin.users.destroy',
])->middleware(['auth', 'role:Administrador']);
