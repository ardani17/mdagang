<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckLowStock::class,
        Commands\CheckOverdueInvoices::class,
        Commands\GenerateDailyReport::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check low stock every 6 hours
        $schedule->command('stock:check-low')
            ->everyFourHours()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/low-stock.log'));

        // Check overdue invoices daily at 9 AM
        $schedule->command('invoices:check-overdue')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/overdue-invoices.log'));

        // Generate daily report at midnight
        $schedule->command('reports:daily')
            ->dailyAt('00:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/daily-reports.log'));

        // Clean up old activity logs (older than 90 days)
        $schedule->call(function () {
            \App\Models\ActivityLog::where('created_at', '<', now()->subDays(90))->delete();
        })->weekly()->sundays()->at('02:00');

        // Backup database daily at 2 AM
        if (config('app.env') === 'production') {
            $schedule->command('backup:run')
                ->dailyAt('02:00')
                ->withoutOverlapping()
                ->runInBackground();
        }

        // Clear expired password reset tokens weekly
        $schedule->command('auth:clear-resets')
            ->weekly()
            ->sundays()
            ->at('01:00');

        // Clear expired sanctum tokens monthly
        $schedule->command('sanctum:prune-expired')
            ->monthly()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}