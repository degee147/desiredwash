<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Console\Command;

class BackfillOrderType extends Command
{
    /**
     * php artisan orders:backfill-order-type
     * php artisan orders:backfill-order-type --dry-run
     * php artisan orders:backfill-order-type --tolerance=0.05
     */
    protected $signature = 'orders:backfill-order-type
                            {--dry-run : Show what would change without saving}
                            {--tolerance=0.02 : Allowed fractional difference when matching express pricing (default 2%)}';

    protected $description = 'Recover order_type for orders created before order_type was added to Order::$fillable. '
        . 'Compares each item\'s stored unit_price against the service\'s current base price to detect express pricing.';

    public function handle(): int
    {
        $dryRun    = (bool) $this->option('dry-run');
        $tolerance = (float) $this->option('tolerance');

        $services = Service::all()->keyBy('id');

        // Only orders currently marked 'standard' are candidates — express orders
        // created after the fillable fix are already correct and untouched.
        $orders = Order::where('order_type', 'standard')
            ->orWhereNull('order_type')
            ->get();

        $this->info("Scanning {$orders->count()} order(s) marked 'standard'...");

        $toUpdate = collect();

        foreach ($orders as $order) {
            $items = is_array($order->items) ? $order->items : json_decode($order->items, true);

            if (empty($items)) {
                continue;
            }

            $looksExpress = false;

            foreach ($items as $item) {
                $service = $services->get($item['service_id'] ?? null);
                if (!$service) {
                    continue;
                }

                $basePrice = (float) $service->price;
                $unitPrice = (float) ($item['unit_price'] ?? 0);

                if ($basePrice <= 0) {
                    continue;
                }

                $ratio = $unitPrice / $basePrice;

                // Express multiplier is typically 1.8 — flag anything
                // meaningfully above 1.0x base price (with tolerance for
                // float rounding) as likely express.
                if ($ratio > (1.0 + $tolerance)) {
                    $looksExpress = true;
                    break;
                }
            }

            if ($looksExpress) {
                $toUpdate->push($order);
            }
        }

        if ($toUpdate->isEmpty()) {
            $this->info('No orders detected as mispriced express. Nothing to do.');
            return self::SUCCESS;
        }

        $this->warn("Detected {$toUpdate->count()} order(s) priced as express but labeled 'standard':");

        $this->table(
            ['Order ID', 'Total', 'Created'],
            $toUpdate->map(fn($o) => [
                substr($o->id, 0, 8) . '…',
                '₦' . number_format((float) $o->total, 2),
                $o->created_at?->format('M j, Y g:i a'),
            ])
        );

        if ($dryRun) {
            $this->comment('Dry run — no changes saved. Remove --dry-run to apply.');
            return self::SUCCESS;
        }

        if (!$this->confirm('Update these orders to order_type = express?', true)) {
            $this->comment('Cancelled. No changes made.');
            return self::SUCCESS;
        }

        $toUpdate->each(fn($o) => $o->update(['order_type' => 'express']));

        $this->info("Updated {$toUpdate->count()} order(s) to order_type = express.");

        return self::SUCCESS;
    }
}
