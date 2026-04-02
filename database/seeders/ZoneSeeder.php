<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['id' => 'z01', 'name' => 'GRA Phase 1', 'area' => 'GRA', 'is_available' => true, 'delivery_fee' => 500],
            ['id' => 'z02', 'name' => 'GRA Phase 2', 'area' => 'GRA', 'is_available' => true, 'delivery_fee' => 500],
            ['id' => 'z03', 'name' => 'GRA Phase 3', 'area' => 'GRA', 'is_available' => true, 'delivery_fee' => 500],
            ['id' => 'z04', 'name' => 'Old GRA', 'area' => 'Old GRA', 'is_available' => true, 'delivery_fee' => 600],
            ['id' => 'z05', 'name' => 'Rumuola', 'area' => 'Rumuola', 'is_available' => true, 'delivery_fee' => 700],
            ['id' => 'z06', 'name' => 'Rumuokoro', 'area' => 'Rumuokoro', 'is_available' => true, 'delivery_fee' => 700],
            ['id' => 'z07', 'name' => 'Rumuomasi', 'area' => 'Rumuomasi', 'is_available' => true, 'delivery_fee' => 700],
            ['id' => 'z08', 'name' => 'Rumuigbo', 'area' => 'Rumuigbo', 'is_available' => true, 'delivery_fee' => 700],
            ['id' => 'z09', 'name' => 'Rumuobiakani', 'area' => 'Rumuobiakani', 'is_available' => true, 'delivery_fee' => 750],
            ['id' => 'z10', 'name' => 'Woji', 'area' => 'Woji', 'is_available' => true, 'delivery_fee' => 800],
            ['id' => 'z11', 'name' => 'Trans-Amadi', 'area' => 'Trans-Amadi', 'is_available' => true, 'delivery_fee' => 700],
            ['id' => 'z12', 'name' => 'Diobu', 'area' => 'Diobu', 'is_available' => true, 'delivery_fee' => 600],
            ['id' => 'z13', 'name' => 'Borokiri', 'area' => 'Borokiri', 'is_available' => true, 'delivery_fee' => 650],
            ['id' => 'z14', 'name' => 'Ogbunabali', 'area' => 'Ogbunabali', 'is_available' => true, 'delivery_fee' => 650],
            ['id' => 'z15', 'name' => 'Ekoro', 'area' => 'Ekoro', 'is_available' => true, 'delivery_fee' => 800],
            ['id' => 'z16', 'name' => 'Nkpolu-Oroworukwo', 'area' => 'Nkpolu', 'is_available' => true, 'delivery_fee' => 700],
            ['id' => 'z17', 'name' => 'Rukpokwu', 'area' => 'Rukpokwu', 'is_available' => true, 'delivery_fee' => 850],
            ['id' => 'z18', 'name' => 'Rumuepirikom', 'area' => 'Rumuepirikom', 'is_available' => true, 'delivery_fee' => 800],
            ['id' => 'z19', 'name' => 'Eliozu', 'area' => 'Eliozu', 'is_available' => true, 'delivery_fee' => 900],
            ['id' => 'z20', 'name' => 'Obia-Akpor', 'area' => 'Obia-Akpor', 'is_available' => true, 'delivery_fee' => 800],
            ['id' => 'z21', 'name' => 'Choba', 'area' => 'Choba', 'is_available' => true, 'delivery_fee' => 900],
            ['id' => 'z22', 'name' => 'Ozuoba', 'area' => 'Ozuoba', 'is_available' => true, 'delivery_fee' => 900],
            ['id' => 'z23', 'name' => 'Alakahia', 'area' => 'Alakahia', 'is_available' => true, 'delivery_fee' => 900],
            ['id' => 'z24', 'name' => 'Rumuche', 'area' => 'Rumuche', 'is_available' => false, 'delivery_fee' => 900],
        ];

        foreach ($zones as $zone) {
            DB::table('zones')->updateOrInsert(['id' => $zone['id']], array_merge($zone, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
