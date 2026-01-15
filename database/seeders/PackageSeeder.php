<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Standard Package',
                'subtitle' => '50 Clothes Per Month',
                'price' => 34900.00,
                'old_price' => null,
                'icon_class' => 'icons-2840435',
                'sort_order' => 1,
                'is_featured' => false,
                'is_active' => true,
                'items' => [
                    '4 T-Shirts',
                    '1 Pairs of Jeans',
                    '3 Button-Down Shirts',
                    '1 Pair of Shorts',
                    '7 Pairs of Underwear',
                    '6 Pairs of Socks',
                    '1 Towel',
                    '1 Set of Sheets'
                ]
            ],
            [
                'name' => 'Enterprise Package',
                'subtitle' => '60 Clothes Per Month',
                'price' => 39000.00,
                'old_price' => null,
                'icon_class' => 'icons-2840421',
                'sort_order' => 2,
                'is_featured' => true,
                'is_active' => true,
                'items' => [
                    '4 T-Shirts',
                    '2 Pairs of Jeans',
                    '4 Button-Down Shirts',
                    '2 Pair of Shorts',
                    '8 Pairs of Underwear',
                    '6 Pairs of Socks',
                    '2 Towel',
                    '2 Set of Sheets'
                ]
            ],
            [
                'name' => 'Premium Package',
                'subtitle' => '80 Clothes Per Month',
                'price' => 44900.00,
                'old_price' => 44900.00, // Same price, no discount currently
                'icon_class' => 'icons-2230769',
                'sort_order' => 3,
                'is_featured' => false,
                'is_active' => true,
                'items' => [
                    '6 T-Shirts',
                    '3 Pairs of Jeans',
                    '4 Button-Down Shirts',
                    '2 Pair of Shorts',
                    '9 Pairs of Underwear',
                    '8 Pairs of Socks',
                    '2 Towel',
                    '2 Set of Sheets'
                ]
            ]
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
