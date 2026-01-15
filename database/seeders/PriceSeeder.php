<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prices = [
            // Clothing
            ['item_name' => 'T-shirts / Polo', 'category' => 'clothing', 'service_type' => 'wash_press', 'regular_price' => 800, 'express_price' => 1400, 'icon_class' => 'icons-884417', 'description' => 'Washed and Pressed'],
            ['item_name' => 'Trousers / Joggers', 'category' => 'clothing', 'service_type' => 'wash_press', 'regular_price' => 1000, 'express_price' => 1600, 'icon_class' => 'icons-1233149'],
            ['item_name' => 'Shirts', 'category' => 'clothing', 'service_type' => 'wash_press', 'regular_price' => 1000, 'express_price' => 1600, 'icon_class' => 'icons-884417', 'description' => 'Washed and Pressed'],
            ['item_name' => 'Long / Flaired Shirts', 'category' => 'clothing', 'regular_price' => 1000, 'express_price' => 1800],
            ['item_name' => 'Short Skirts', 'category' => 'clothing', 'regular_price' => 900, 'express_price' => 1400],
            ['item_name' => 'Women Blouse', 'category' => 'clothing', 'regular_price' => 1200, 'express_price' => 2000],
            ['item_name' => 'Gowns (long)', 'category' => 'clothing', 'service_type' => 'dry_clean', 'regular_price' => 1500, 'express_price' => 2400, 'icon_class' => 'icons-868044', 'description' => 'Dry Clean'],
            ['item_name' => 'Gowns (short)', 'category' => 'clothing', 'regular_price' => 1300, 'express_price' => 2000],
            ['item_name' => 'Children Ball Gown', 'category' => 'clothing', 'regular_price' => 3000, 'express_price' => 4000],
            ['item_name' => 'Jumpsuit', 'category' => 'clothing', 'regular_price' => 1500, 'express_price' => 2000],
            ['item_name' => 'Women Gown (Native)', 'category' => 'clothing', 'regular_price' => 1500, 'express_price' => 2000],
            ['item_name' => 'Blazer', 'category' => 'clothing', 'regular_price' => 2500, 'express_price' => 3600],
            ['item_name' => 'Suits', 'category' => 'clothing', 'regular_price' => 2000, 'express_price' => 2500],
            ['item_name' => 'Safari – Suits', 'category' => 'clothing', 'regular_price' => 3000, 'express_price' => 4000],
            ['item_name' => 'Top Shirt', 'category' => 'clothing', 'regular_price' => 1000, 'express_price' => 1200],
            ['item_name' => 'Women Tops', 'category' => 'clothing', 'regular_price' => 700, 'express_price' => null],
            ['item_name' => 'Complete Suit Set', 'category' => 'clothing', 'regular_price' => 4000, 'express_price' => null],

            // Traditional Wear
            ['item_name' => 'Otigbo', 'category' => 'traditional', 'regular_price' => 2000, 'express_price' => 3000],
            ['item_name' => 'Senator', 'category' => 'traditional', 'regular_price' => 2000, 'express_price' => 3000],
            ['item_name' => 'Women Beaded Blouse', 'category' => 'traditional', 'regular_price' => 3000, 'express_price' => 4000],
            ['item_name' => 'Women Beaded Gown', 'category' => 'traditional', 'regular_price' => 4000, 'express_price' => 5000],
            ['item_name' => 'Academic Gowns', 'category' => 'traditional', 'regular_price' => 2000, 'express_price' => 2500],
            ['item_name' => 'Agbada', 'category' => 'traditional', 'regular_price' => 3500, 'express_price' => 5000],
            ['item_name' => 'Wedding Gown', 'category' => 'special', 'regular_price' => 5000, 'express_price' => 8000],

            // Bedding
            ['item_name' => 'Bed Sheets', 'category' => 'bedding', 'service_type' => 'wash_press', 'regular_price' => 1500, 'express_price' => 2400, 'icon_class' => 'icons-495018', 'description' => 'Bed Set (Wash and Press)'],
            ['item_name' => 'Wrapper', 'category' => 'bedding', 'regular_price' => 700, 'express_price' => 1000],
            ['item_name' => 'Beaded Wrapper', 'category' => 'bedding', 'regular_price' => 3500, 'express_price' => 6000],
            ['item_name' => 'Small Duvet', 'category' => 'bedding', 'regular_price' => 2000, 'express_price' => 3000],
            ['item_name' => 'Big (Heavy) Blankets', 'category' => 'bedding', 'service_type' => 'wash_press', 'regular_price' => 3000, 'express_price' => 4000, 'icon_class' => 'icons-2737832', 'description' => 'Washed and Pressed'],
            ['item_name' => 'Big (Heavy Duvet)', 'category' => 'bedding', 'regular_price' => 4000, 'express_price' => 5000],
            ['item_name' => 'Small Blankets', 'category' => 'bedding', 'regular_price' => 2000, 'express_price' => 3000],
            ['item_name' => 'Curtains (Heavy Drapes)', 'category' => 'bedding', 'service_type' => 'wash_press', 'regular_price' => 2500, 'express_price' => 3500, 'icon_class' => 'icons-863958', 'description' => 'Washed and Pressed'],
            ['item_name' => 'Curtains (Light)', 'category' => 'bedding', 'regular_price' => 1700, 'express_price' => 2400],

            // Accessories
            ['item_name' => 'Short Nicker', 'category' => 'accessories', 'regular_price' => 700, 'express_price' => null],
            ['item_name' => 'Towels (Small)', 'category' => 'accessories', 'regular_price' => 700, 'express_price' => 1000],
            ['item_name' => 'Towels (Big)', 'category' => 'accessories', 'regular_price' => 1200, 'express_price' => 2000],
            ['item_name' => 'Sweater / Sweater Shirt', 'category' => 'accessories', 'regular_price' => 1200, 'express_price' => 1500],
            ['item_name' => 'Parkas / Winter Jacket', 'category' => 'accessories', 'regular_price' => 1200, 'express_price' => 2000],
            ['item_name' => 'Coverall (Up & Down)', 'category' => 'accessories', 'regular_price' => 2500, 'express_price' => 3500],
            ['item_name' => 'Coverall (Full)', 'category' => 'accessories', 'regular_price' => 3000, 'express_price' => 5000],
            ['item_name' => 'Boxers / Singlets', 'category' => 'accessories', 'regular_price' => 600, 'express_price' => null],
            ['item_name' => 'Camisoles', 'category' => 'accessories', 'regular_price' => 600, 'express_price' => null],
            ['item_name' => 'Ties / Pillowcase', 'category' => 'accessories', 'regular_price' => 300, 'express_price' => 400],
            ['item_name' => 'Socks', 'category' => 'accessories', 'regular_price' => 300, 'express_price' => 400],
            ['item_name' => 'Head Tie / Head Wrapper', 'category' => 'accessories', 'regular_price' => 300, 'express_price' => 400],
            ['item_name' => 'Gele / Caps', 'category' => 'accessories', 'regular_price' => 300, 'express_price' => 400],
        ];

        foreach ($prices as $price) {
            Price::create($price);
        }
    }
}
