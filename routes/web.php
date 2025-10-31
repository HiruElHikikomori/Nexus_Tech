<?php

use Illuminate\Support\Facades\Route;

// ========================
// Controladores (imports)
// ========================
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\userController as AdminUsersController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Users\CartItemsController;
use App\Http\Controllers\Users\AccountController;
use App\Http\Controllers\Admin\AdminAccountController;

// Nuevos / ya importados
use App\Http\Controllers\Users\ReviewsController;
use App\Http\Controllers\Admin\ReportsController as AdminReportsController;
use App\Http\Controllers\Users\UserProductsController;
use App\Http\Controllers\Users\UserCatalogController;
use App\Http\Controllers\Admin\UserProductsAdminController;

// ===================================
// Home / público
// ===================================
Route::get('/', [AdminProductController::class, 'RandomProductOrder'])->name('index');
Route::get('/aboutus', fn () => view('aboutus'))->name('aboutus');

// ===================================
// Autenticación básica
// ===================================
// Registro
Route::get('/register', [RegisterController::class, 'ShowRegisterForm'])
    ->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'Register'])
    ->middleware('guest');

// Login
Route::get('/login', [LoginController::class, 'ShowLoginForm'])
    ->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'Login'])
    ->middleware('guest');

// Logout
Route::post('/logout', [LoginController::class, 'Logout'])
    ->middleware('auth', 'role:Usuario')->name('logout');

// ===================================
// Catálogo público principal (productos oficiales)
// ===================================
Route::get('/products', [AdminProductController::class, 'ProductUser'])
    ->name('user.product'); // ya la tenías
Route::get('/products/search', [AdminProductController::class, 'search'])
    ->name('products.search');

// ===================================
// Carrito (usuario autenticado)
// ===================================
Route::middleware('auth', 'role:Usuario')->group(function () {
    // Vista del carrito
    Route::get('/cart', [CartItemsController::class, 'index'])->name('cart.index');

    // Agregar al carrito (desde tarjeta de producto)
    Route::post('/products', [CartItemsController::class, 'store'])->name('cart.store');

    // Actualizar cantidad
    Route::put('/cart/{cartItem}', [CartItemsController::class, 'update'])->name('cart.update');

    // Quitar del carrito
    Route::delete('/cart/{cartItem}', [CartItemsController::class, 'destroy'])->name('cart.destroy');
});

// ===================================
// Perfil de usuario (datos y cuenta)
// ===================================
Route::middleware('auth', 'role:Usuario')->group(function () {
    Route::get('/userProfile', [AccountController::class, 'show'])->name('user.profile');
    Route::get('/users/{userId}/edit', [AccountController::class, 'edit'])->name('users.edit');
    Route::put('/users/{userId}', [AccountController::class, 'update'])->name('users.update');
});

// ===================================
// Catálogo de piezas publicadas por usuarios (público)
// ===================================
Route::get('/catalogo-usuarios', [UserCatalogController::class,'index'])
    ->name('user_catalog.index');

// ===================================
// Reseñas (producto oficial o pieza de usuario)
// ===================================
Route::middleware('auth', 'role:Usuario')->group(function () {
    Route::post('/reviews', [ReviewsController::class,'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewsController::class,'destroy'])->name('reviews.destroy');
});

// ===================================
// “Mis piezas” (usuarios suben sus piezas) - autenticado
// ===================================
Route::prefix('/users/{userId}/piezas')
    ->name('users.user_products.')
    ->middleware('auth', 'role:Usuario')
    ->group(function () {
        Route::get('/',              [UserProductsController::class, 'index'])->name('index');
        Route::post('/',             [UserProductsController::class, 'store'])->name('store');
        Route::put('/{userProduct}', [UserProductsController::class, 'update'])->name('update');
        Route::delete('/{userProduct}', [UserProductsController::class, 'destroy'])->name('destroy'); // <-- FIX
    });

// ===================================
// Zona Admin (auth + rol de admin) — ajusta el middleware de rol a tu implementación
// ===================================
Route::prefix('admin')
    ->middleware(['auth', 'role:Administrador']) // agrega 'role:Admin' si ya lo usas
    ->group(function () {

        // Panel / perfil admin
        Route::get('/profile', [AdminAccountController::class, 'profile'])->name('admin.profile');
        Route::get('/profile/edit', [AdminAccountController::class, 'edit'])->name('admin.profile.edit');
        Route::put('/profile', [AdminAccountController::class, 'update'])->name('admin.profile.update');

        // Gestión de usuarios (admin)
        Route::get('/users', [AdminUsersController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}/edit', [AdminUsersController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [AdminUsersController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [AdminUsersController::class, 'destroy'])->name('admin.users.destroy');

        // Gestión de productos oficiales (admin)
        Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');

        // Reportes (admin)
        Route::get('/reports',                [AdminReportsController::class,'index'])->name('admin.reports.index');
        Route::get('/reports/{report}',       [AdminReportsController::class,'show'])->name('admin.reports.show');
        Route::post('/reports/{report}/resolve', [AdminReportsController::class,'resolve'])->name('admin.reports.resolve');
        Route::delete('/reports/{report}',    [AdminReportsController::class,'destroy'])->name('admin.reports.destroy');

        // Moderación de piezas de usuarios
        Route::get('/user-products/{user}/piezas', [UserProductsAdminController::class, 'listByUser'])
            ->name('admin.user_products.index');
        Route::delete('/user-products/piezas/{userProduct}', [UserProductsAdminController::class, 'destroy'])
            ->name('admin.user_products.destroy');
    });
