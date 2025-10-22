<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductType;

class CreateProductType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:create-product-type {name} {description}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un nuevo tipo de producto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $description = $this->argument('description');


        $productType = ProductType::Create([

            'name' => $name,
            'description' => $description,

        ]);

        $this->info("Tipo de producto '{$productType->name}' creado con éxito");
        return Command::SUCCESS;
    }
}
