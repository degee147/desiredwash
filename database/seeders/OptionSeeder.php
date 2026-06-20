<?php

namespace Database\Seeders;

use App\Models\Option;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    /**
     * Seed default app-wide settings.
     *
     * Uses Option::set() (updateOrCreate) so it's safe to re-run —
     * existing values configured via the admin panel are left untouched
     * only on first run; subsequent runs would overwrite manual edits,
     * so this is intended to run once during initial setup.
     */
    public function run(): void
    {
        $defaults = [
            'express_multiplier'          => '1.8',
            'standard_order_label'        => 'Standard',
            'standard_order_subtitle'     => 'Normal turnaround time',
            'express_order_label'         => 'Express',
            'express_order_subtitle'      => 'Priority processing',
            'express_order_badge'         => '{multiplier}x price',
            'express_order_summary_label' => 'Express Order ({multiplier}x price)',
        ];

        foreach ($defaults as $key => $value) {
            // Only set if not already present — avoids clobbering admin edits on re-seed
            if (Option::where('key', $key)->doesntExist()) {
                Option::set($key, $value);
            }
        }
    }
}
