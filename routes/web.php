<?php

use Illuminate\Support\Facades\Route;

// ========================
// Controladores (imports)
// ========================
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;

// Admin
use App\Http\Controllers\Admin\userController as AdminUsersController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\ReportsController as AdminReportsController;
use App\Http\Controllers\Admin\UserProductsAdminController;

// Users
use App\Http\Controllers\Users\CartItemsController;
use App\Http\Controllers\Users\AccountController;
use App\Http\Controllers\Users\ReviewsController;
use App\Http\Controllers\Users\UserProductsController;
use App\Http\Controllers\Users\UserCatalogController;
use App\Http\Controllers\Users\ReportsController as UserReportsController;

// ===================================
// Home / público
// ===================================

Route::get('/', [AdminProductController::class, 'RandomProductOrder'])
    ->name('index');

Route::get('/aboutus', fn () => view('aboutus'))
    ->name('aboutus');

// ===================================
// Autenticación básica
// ===================================

// Registro
Route::get('/register', [RegisterController::class, 'ShowRegisterForm'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisterController::class, 'Register'])
    ->middleware('guest');

// Login
Route::get('/login', [LoginController::class, 'ShowLoginForm'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [LoginController::class, 'Login'])
    ->middleware('guest');

// Logout (cualquier usuario autenticado)
Route::post('/logout', [LoginController::class, 'Logout'])
    ->middleware('auth')
    ->name('logout');

// ===================================
// Catálogo público principal (productos oficiales)
// ===================================

// Listado público de productos
Route::get('/products', [AdminProductController::class, 'ProductUser'])
    ->name('user.product');

// Búsqueda de productos
Route::get('/products/search', [AdminProductController::class, 'search'])
    ->name('products.search');

// ===================================
// Carrito (usuario autenticado - rol Usuario)
// ===================================

Route::middleware(['auth', 'role:Usuario'])->group(function () {
    // Vista del carrito
    Route::get('/cart', [CartItemsController::class, 'index'])
        ->name('cart.index');

    // Agregar al carrito (desde tarjeta de producto)
    Route::post('/products', [CartItemsController::class, 'store'])
        ->name('cart.store');

    // Actualizar cantidad
    Route::patch('/cart/{cartItem}', [CartItemsController::class, 'updateQuantity'])
        ->name('cart.update');

    // Checkout (vaciar carrito después de comprar)
    Route::post('/cart/checkout', [CartItemsController::class, 'deleteAll'])
        ->name('cart.checkout');

    // Quitar un ítem del carrito
    Route::delete('/cart/{cartItem}', [CartItemsController::class, 'destroy'])
        ->name('cart.destroy');
});

// ===================================
// Perfil de usuario (datos y cuenta) - rol Usuario
// ===================================

Route::middleware(['auth', 'role:Usuario'])->group(function () {
    Route::get('/userProfile', [AccountController::class, 'show'])
        ->name('user.profile');

    Route::get('/users/{userId}/edit', [AccountController::class, 'edit'])
        ->name('users.edit');

    Route::put('/users/{userId}', [AccountController::class, 'update'])
        ->name('users.update');
});

// ===================================
// Catálogo de piezas publicadas por usuarios (público)
// ===================================

Route::get('/catalogo-usuarios', [UserCatalogController::class, 'index'])
    ->name('user_catalog.index');

// ===================================
// Reseñas (producto oficial o pieza de usuario)
// ===================================

// 🔒 Acciones reales (crear / borrar) — SOLO rol Usuario
Route::middleware(['auth', 'role:Usuario'])->group(function () {
    Route::post('/reviews', [ReviewsController::class, 'store'])
        ->name('reviews.store');

    Route::delete('/reviews/{review}', [ReviewsController::class, 'destroy'])
        ->name('reviews.destroy');
});

// 🌐 Parche amable para GET /reviews (evita 405 y redirige)
Route::get('/reviews', function () {
    $prev = url()->previous();

    if ($prev && $prev !== url()->current()) {
        return redirect()->back()->withErrors([
            'reviews' => 'Para dejar una reseña usa el formulario del producto.',
        ]);
    }

    return redirect()->route('user.product')->withErrors([
        'reviews' => 'Para dejar una reseña usa el formulario del producto.',
    ]);
})->name('reviews.index');

// ===================================
// “Mis piezas” (usuarios suben sus piezas) - rol Usuario
// ===================================

Route::prefix('/users/{userId}/piezas')
    ->name('users.user_products.')
    ->middleware(['auth', 'role:Usuario'])
    ->group(function () {
        Route::get('/', [UserProductsController::class, 'index'])->name('index');
        Route::post('/', [UserProductsController::class, 'store'])->name('store');
        Route::put('/{userProduct}', [UserProductsController::class, 'update'])->name('update');
        Route::delete('/{userProduct}', [UserProductsController::class, 'destroy'])->name('destroy');
    });

// ===================================
// Reportes (usuarios autenticados - rol Usuario)
// ===================================

Route::middleware(['auth', 'role:Usuario'])->group(function () {
    Route::post('/reports', [UserReportsController::class, 'store'])
        ->name('reports.store');
});

// ===================================
// Zona Admin (auth + rol Administrador)
// ===================================

Route::prefix('admin')
    ->middleware(['auth', 'role:Administrador'])
    ->group(function () {

        // Dashboard / panel de control (equivalente a tu viejo /controlPanel)
        Route::get('/', function () {
            return view('admin.controlPanel'); // resources/views/admin/controlPanel.blade.php
        })->name('admin.dashboard');

        // Panel / perfil admin
        Route::get('/profile', [AdminAccountController::class, 'show'])
            ->name('admin.profile');

        Route::get('/profile/edit', [AdminAccountController::class, 'edit'])
            ->name('admin.profile.edit');

        Route::put('/profile', [AdminAccountController::class, 'update'])
            ->name('admin.profile.update');

        // Gestión de usuarios (admin)
        Route::get('/users', [AdminUsersController::class, 'index'])
            ->name('admin.users.index');

        Route::get('/users/{user}/edit', [AdminUsersController::class, 'edit'])
            ->name('admin.users.edit');

        Route::put('/users/{user}', [AdminUsersController::class, 'update'])
            ->name('admin.users.update');

        Route::delete('/users/{user}', [AdminUsersController::class, 'destroy'])
            ->name('admin.users.destroy');

        // Gestión de productos oficiales (admin)
        Route::get('/products', [AdminProductController::class, 'index'])
            ->name('admin.products.index');

        Route::get('/products/create', [AdminProductController::class, 'create'])
            ->name('admin.products.create');

        Route::post('/products', [AdminProductController::class, 'store'])
            ->name('admin.products.store');

        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])
            ->name('admin.products.edit');

        Route::put('/products/{product}', [AdminProductController::class, 'update'])
            ->name('admin.products.update');

        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])
            ->name('admin.products.destroy');

        // Reportes (admin)
        Route::get('/reports', [AdminReportsController::class, 'index'])
            ->name('admin.reports.index');

        Route::get('/reports/{report}', [AdminReportsController::class, 'show'])
            ->name('admin.reports.show');

        Route::post('/reports/{report}/resolve', [AdminReportsController::class, 'resolve'])
            ->name('admin.reports.resolve');

        Route::delete('/reports/{report}', [AdminReportsController::class, 'destroy'])
            ->name('admin.reports.destroy');

        // Moderación de piezas de usuarios
        Route::get('/user-products/{user}/piezas', [UserProductsAdminController::class, 'listByUser'])
            ->name('admin.user_products.index');

        Route::delete('/user-products/piezas/{userProduct}', [UserProductsAdminController::class, 'destroy'])
            ->name('admin.user_products.destroy');
    });

/*
|--------------------------------------------------------------------------
| Rutas "antiguas" para compatibilidad (controlPanel, adminProfile)
|--------------------------------------------------------------------------
| Estas mantienen vivas las URLs viejas usadas en tus vistas/blade:
|   - /controlPanel  -> redirige al nuevo dashboard admin
|   - /adminProfile  -> redirige al nuevo perfil admin
| Siguen protegidas por auth + rol Administrador.
*/

Route::middleware(['auth', 'role:Administrador'])->group(function () {
    // Vieja URL del panel de control → ahora sí al panel nuevo
    Route::get('/controlPanel', function () {
        return redirect()->route('admin.dashboard');
    })->name('controlPanel.legacy');

    // Vieja URL del perfil de admin
    Route::get('/adminProfile', function () {
        return redirect()->route('admin.profile');
    })->name('adminProfile.legacy');
});
