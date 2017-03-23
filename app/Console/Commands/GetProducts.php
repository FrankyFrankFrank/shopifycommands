<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all TRP Products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = file_get_contents('https://' .
                    env('SHOPIFY_API') . ':' .
                    env('SHOPIFY_PASSWORD') .
                    env('SHOPIFY_URL') .
                    'products.json?fields=id,title&limit=250');

        $response = json_decode($response);

        $products = collect($response->products)->map( function($product) {
            return (array)$product;
        });

        $this->table(['id', 'title'], $products);

    }
}
