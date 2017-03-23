<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GetAllCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all TRP Customers';

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

        $count = file_get_contents('https://' .
                    env('SHOPIFY_API') . ':' .
                    env('SHOPIFY_PASSWORD') .
                    env('SHOPIFY_URL') .
                    'customers/count.json');
        $count = json_decode($count)->count;

        $pages = (int)ceil($count / 250);

        $this->line('Fetching ' . $count . " customers...");

        $bar = $this->output->createProgressBar($pages);

        $customers = collect(range(1, $pages))->flatMap(function($x) use ($bar) {

            $bar->advance();

            $chunk =  file_get_contents('https://' .
                    env('SHOPIFY_API') . ':' .
                    env('SHOPIFY_PASSWORD') .
                    env('SHOPIFY_URL') .
                    'customers.json?'.
                    '&limit=250' .
                    '&page=' . $x );

            return json_decode($chunk)->customers;
        });

        $customers = $customers->sortBy('total_spent');

        $log = 'First Name, Last Name, Total Spent' . "\n";
        foreach($customers as $customer) {
            $log = $log .   $customer->first_name . ',' .
                            $customer->last_name . "," .
                            $customer->total_spent . "\n";
        };

        Storage::put('log.csv', $log);

    }
}
