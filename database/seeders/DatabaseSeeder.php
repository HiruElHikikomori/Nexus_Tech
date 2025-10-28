<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\ProductType;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- ROLES ----
        Role::firstOrCreate(['rol_id' => 1], ['name' => 'Administrador']);
        Role::firstOrCreate(['rol_id' => 2], ['name' => 'Usuario']);

        // ---- TIPOS DE PRODUCTO ----
        ProductType::firstOrCreate(
            ['product_type_id' => 1],
            ['name' => 'GPU', 'description' => 'Tarjetas gráficas']
        );
        ProductType::firstOrCreate(
            ['product_type_id' => 2],
            ['name' => 'CPU', 'description' => 'Procesadores']
        );
        ProductType::firstOrCreate(
            ['product_type_id' => 3],
            ['name' => 'RAM', 'description' => 'Memoria']
        );

        // ---- ADMIN POR DEFECTO ----
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'last_name' => 'Root',
                'email' => 'admin@nexustech.dev',
                'phone_number' => '0000000000',
                'address' => 'N/A',
                'profile_img_name' => 'default.png',
                'rol_id' => 1, // Administrador
                'password' => Hash::make('admin123'), // cámbiala luego
            ]
        );

        // ---- CATALOGO BASE (opcional) ----
        // Nota: asegúrate de tener DECIMAL(10,2) en products.price
        Product::firstOrCreate(
            ['name' => 'GeForce RTX 3060'],
            [
                'product_type_id' => 1,
                'description' => 'GPU 12GB GDDR6',
                'price' => 6999.00,
                'stock' => 5,
                'img_name' => 'default.png',
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Ryzen 5 5600'],
            [
                'product_type_id' => 2,
                'description' => 'CPU 6c/12t',
                'price' => 2899.00,
                'stock' => 8,
                'img_name' => 'default.png',
            ]
        );

        Product::firstOrCreate(
            ['name' => 'RAM DDR4 16GB (2x8)'],
            [
                'product_type_id' => 3,
                'description' => '3200MHz CL16',
                'price' => 899.00,
                'stock' => 12,
                'img_name' => 'default.png',
            ]
        );
    }
}
