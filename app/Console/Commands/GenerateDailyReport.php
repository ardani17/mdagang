<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\ProductionOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GenerateDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily business report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating daily report for ' . Carbon::now()->format('Y-m-d'));

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Sales metrics
        $todayOrders = Order::whereDate('order_date', $today)->get();
        $todayRevenue = $todayOrders->where('status', 'completed')->sum('total_amount');
        $newOrders = $todayOrders->count();
        $completedOrders = $todayOrders->where('status', 'completed')->count();

        // Financial metrics
        $todayInvoices = Invoice::whereDate('invoice_date', $today)->get();
        $invoicesGenerated = $todayInvoices->count();
        $invoiceAmount = $todayInvoices->sum('total_amount');
        
        $todayPayments = Payment::whereDate('payment_date', $today)->get();
        $paymentsReceived = $todayPayments->where('status', 'completed')->count();
        $paymentAmount = $todayPayments->where('status', 'completed')->sum('amount');

        // Production metrics
        $todayProduction = ProductionOrder::whereDate('created_at', $today)->get();
        $productionStarted = $todayProduction->count();
        $productionCompleted = $todayProduction->where('status', 'completed')->count();

        // Cash flow
        $income = Transaction::income()->completed()
            ->whereDate('transaction_date', $today)
            ->sum('amount');
        
        $expense = Transaction::expense()->completed()
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $netCashFlow = $income - $expense;

        // Prepare report data
        $report = [
            'date' => $today->format('Y-m-d'),
            'sales' => [
                'new_orders' => $newOrders,
                'completed_orders' => $completedOrders,
                'revenue' => $todayRevenue,
            ],
            'financial' => [
                'invoices_generated' => $invoicesGenerated,
                'invoice_amount' => $invoiceAmount,
                'payments_received' => $paymentsReceived,
                'payment_amount' => $paymentAmount,
            ],
            'production' => [
                'orders_started' => $productionStarted,
                'orders_completed' => $productionCompleted,
            ],
            'cash_flow' => [
                'income' => $income,
                'expense' => $expense,
                'net' => $netCashFlow,
            ],
        ];

        // Display report in console
        $this->info('=== DAILY BUSINESS REPORT ===');
        $this->info('Date: ' . $today->format('l, F j, Y'));
        $this->info('');
        
        $this->info('SALES METRICS:');
        $this->line('  New Orders: ' . $newOrders);
        $this->line('  Completed Orders: ' . $completedOrders);
        $this->line('  Revenue: Rp ' . number_format($todayRevenue, 2));
        $this->info('');
        
        $this->info('FINANCIAL METRICS:');
        $this->line('  Invoices Generated: ' . $invoicesGenerated);
        $this->line('  Invoice Amount: Rp ' . number_format($invoiceAmount, 2));
        $this->line('  Payments Received: ' . $paymentsReceived);
        $this->line('  Payment Amount: Rp ' . number_format($paymentAmount, 2));
        $this->info('');
        
        $this->info('PRODUCTION METRICS:');
        $this->line('  Production Started: ' . $productionStarted);
        $this->line('  Production Completed: ' . $productionCompleted);
        $this->info('');
        
        $this->info('CASH FLOW:');
        $this->line('  Income: Rp ' . number_format($income, 2));
        $this->line('  Expense: Rp ' . number_format($expense, 2));
        if ($netCashFlow >= 0) {
            $this->info('  Net: Rp ' . number_format($netCashFlow, 2));
        } else {
            $this->warn('  Net: -Rp ' . number_format(abs($netCashFlow), 2));
        }

        // Log the report
        Log::info('Daily report generated', $report);

        // Send report to administrators
        $this->sendReportToAdmins($report);

        $this->info('');
        $this->info('Daily report generated successfully!');

        return Command::SUCCESS;
    }

    /**
     * Send report to administrators
     */
    private function sendReportToAdmins($report)
    {
        $admins = User::where('role', 'Administrator')->get();

        foreach ($admins as $admin) {
            // In a real application, send email with report
            // Mail::to($admin->email)->send(new DailyReport($report));
            
            Log::info("Daily report sent to {$admin->email}");
        }
    }
}