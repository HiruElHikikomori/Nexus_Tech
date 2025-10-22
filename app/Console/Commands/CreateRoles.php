<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;

class CreateRoles extends Command
{

    protected $signature = 'rol:create {name}';
    protected $description = 'Crea un nuevo rol';

    public function handle()
    {
        $name = $this->argument('name');

        $NewRole = Role::create([
            'name' => $name,
        ]);

        $this->info("Rol '{$name}' creado con éxito");
        return Command::SUCCESS;
    }
}
