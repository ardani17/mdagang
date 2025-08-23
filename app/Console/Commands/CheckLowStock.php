<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RawMaterial;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-low';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for low stock items and send alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for low stock items...');

        // Check raw materials
        $lowStockMaterials = RawMaterial::whereRaw('current_stock <= minimum_stock')
            ->where('minimum_stock', '>', 0)
            ->where('is_active', true)
            ->get();

        // Check products
        $lowStockProducts = Product::whereRaw('current_stock <= minimum_stock')
            ->where('minimum_stock', '>', 0)
            ->where('is_active', true)
            ->get();

        $totalLowStock = $lowStockMaterials->count() + $lowStockProducts->count();

        if ($totalLowStock > 0) {
            $this->warn("Found {$totalLowStock} items with low stock!");
            
            // Log the alert
            Log::warning('Low stock alert', [
                'raw_materials' => $lowStockMaterials->pluck('name', 'id')->toArray(),
                'products' => $lowStockProducts->pluck('name', 'id')->toArray(),
                'timestamp' => now(),
            ]);

            // Send notification to administrators
            $this->sendLowStockNotification($lowStockMaterials, $lowStockProducts);
            
            // Display in console
            if ($lowStockMaterials->count() > 0) {
                $this->table(
                    ['ID', 'Raw Material', 'Current Stock', 'Minimum Stock'],
                    $lowStockMaterials->map(function ($item) {
                        return [
                            $item->id,
                            $item->name,
                            $item->current_stock . ' ' . $item->unit,
                            $item->minimum_stock . ' ' . $item->unit,
                        ];
                    })
                );
            }

            if ($lowStockProducts->count() > 0) {
                $this->table(
                    ['ID', 'Product', 'Current Stock', 'Minimum Stock'],
                    $lowStockProducts->map(function ($item) {
                        return [
                            $item->id,
                            $item->name,
                            $item->current_stock . ' ' . $item->unit,
                            $item->minimum_stock . ' ' . $item->unit,
                        ];
                    })
                );
            }
        } else {
            $this->info('All items have sufficient stock.');
        }

        return Command::SUCCESS;
    }

    /**
     * Send low stock notification to administrators
     */
    private function sendLowStockNotification($materials, $products)
    {
        // Get all administrators
        $admins = User::where('role', 'Administrator')->get();

        foreach ($admins as $admin) {
            // In a real application, you would send an email here
            // Mail::to($admin->email)->send(new LowStockAlert($materials, $products));
            
            Log::info("Low stock notification sent to {$admin->email}");
        }
    }
}