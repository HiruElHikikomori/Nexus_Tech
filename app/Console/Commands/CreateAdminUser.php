<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // Importa modelo user
use App\Models\Role; // Importa modelo role
use Illuminate\Support\Facades\Hash; // Importa Hash para encriptar contraseña

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {name} {email} {password} {--username=} {--last_name=} {--phone_number=} {--address=} {--profile_img_name=default.png}';
    protected $description = 'Crea un nuevo administrador';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        $username = $this->option('username') ?: 'admin_' . substr(uniqid(), -5);
        $last_name = $this->option('last_name') ?: 'Admin';
        $phone_number = $this->option('phone_number') ?: '0000000000';
        $address = $this->option('address') ?: 'Admin Address';
        $profile_img_name = $this->option('profile_img_name');

        //Hasheao de contraseña
        $hashedPassword = Hash::make($password);

        //Crea el usuario
        $userAdmin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'username' => $username,
            'last_name' => $last_name,
            'phone_number' => $phone_number,
            'address' => $address,
            'profile_img_name' => $profile_img_name,
            'rol_id' => 1,
        ]);

        $this->info("Usuario administrador '{$userAdmin->email}' creado con éxito");
        return Command::SUCCESS;
    }
}
