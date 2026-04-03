<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Laundry', 'emoji' => '🧺', 'price' => 1500],
            ['name' => 'Dry Cleaning', 'emoji' => '👔', 'price' => 2500],
            ['name' => 'Ironing', 'emoji' => '🔥', 'price' => 1000],
            ['name' => 'Pick Up & Delivery', 'emoji' => '🚚', 'price' => 2000],
            ['name' => 'Stain Removal', 'emoji' => '🧼', 'price' => 1800],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
