<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\userController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Users\CartItemsController;
use App\Http\Controllers\Users\AccountController;
use App\Http\Controllers\Admin\AdminAccountController;




/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/', [ProductController::class, "RandomProductOrder"])-> name('index');

//Registro
Route::get('/register', [RegisterController::class, "ShowRegisterForm"])
    ->middleware('guest')
    ->name('register');
Route::post('/register', [RegisterController::class, "Register"])
    ->middleware('guest');

//Login
Route::get('/login', [LoginController::class, "ShowLoginForm"])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [LoginController::class, "Login"])
    ->middleware('guest');

//Logout
Route::post('/logout', [LoginController::class, "Logout"])-> name('logout');

//About us
Route::get('/aboutus', function(){
    return view('/aboutus');
});

//Páginas protegidas (Usuario)

Route::middleware(['auth', 'role:Usuario'])->group(function() {
    //Productos
    Route::get('/products', [ProductController::class, "ProductUser"])->name('user.product');
    Route::post('/products', [CartItemsController::class, "store"])->name('cart.add-item');

    //Búsquedas
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

    //Carrito
    Route::patch('/cart/{cartItem}/update-quantity', [CartItemsController::class, "updateQuantity"])->name('cart.update-item-quantity');
    Route::delete('/UserCart', [CartItemsController::class, "deleteAll"])->name('delete.cart.items');
    
    //Perfil
    Route::get('/userProfile', [AccountController::class, "show"])->name('user.profile');
    Route::get('/users/{userId}edit', [AccountController::class, 'edit'])->name('users.edit');
    Route::put('/users/{userId}', [AccountController::class, 'update'])->name('users.update');

    Route::resource('/UserCart', CartItemsController::class)->only(['index', 'destroy'])->parameters([
        'UserCart' => 'cartItem',
    ])->names([
        'index' => 'user.cart',
        'destroy' => 'delete.cart.item',
    ]);

    
});



//Solo para el admin

    Route::get('/controlPanel', function(){
        return view('admin.controlPanel');
    })->name('admin.controlPanel')->middleware(['auth', 'role:Administrador']);

    //Busquedas
    Route::get('/AdminProducts/search', [AdminController::class, 'unifiedSearch'])->name('admin.products.search')
    ->middleware(['auth', 'role:Administrador']);

    // Rutas de recurso para productos
    Route::resource('/AdminProducts', ProductController::class)->parameters([
        'AdminProducts' => 'product',
    ])->names([
        'index' => 'admin.products.index',
        'store' => 'admin.products.store',
        'show' => 'admin.products.show',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ])
    ->middleware(['auth', 'role:Administrador']);

    //Perfil
    Route::get('/adminProfile', [AdminAccountController::class, "show"])->name('admin.profile');
    Route::get('/admin/{userId}edit', [AdminAccountController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/users/{userId}', [AdminAccountController::class, 'update'])->name('admin.update');

    //Rutas de recurso para cuentas de usuarios
    Route::resource('/AdminUsers', userController::class)->parameters([
        'AdminUsers' => 'user',
    ])->names([
        'index' => 'admin.users.index',
        'show' => 'admin.users.show',
        'destroy' => 'admin.users.destroy',
    ])
    ->middleware(['auth', 'role:Administrador']);