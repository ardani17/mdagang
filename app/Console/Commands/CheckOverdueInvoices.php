<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue invoices and send reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue invoices...');

        $overdueInvoices = Invoice::where('status', '!=', 'paid')
            ->where('status', '!=', 'void')
            ->where('due_date', '<', Carbon::now())
            ->with(['customer', 'items'])
            ->get();

        if ($overdueInvoices->count() > 0) {
            $this->warn("Found {$overdueInvoices->count()} overdue invoices!");

            $totalOverdueAmount = $overdueInvoices->sum('balance_due');
            
            // Log the overdue invoices
            Log::warning('Overdue invoices detected', [
                'count' => $overdueInvoices->count(),
                'total_amount' => $totalOverdueAmount,
                'invoices' => $overdueInvoices->map(function ($invoice) {
                    return [
                        'invoice_number' => $invoice->invoice_number,
                        'customer' => $invoice->customer->name,
                        'amount_due' => $invoice->balance_due,
                        'days_overdue' => Carbon::now()->diffInDays($invoice->due_date),
                    ];
                })->toArray(),
            ]);

            // Display in console
            $this->table(
                ['Invoice #', 'Customer', 'Amount Due', 'Days Overdue', 'Status'],
                $overdueInvoices->map(function ($invoice) {
                    return [
                        $invoice->invoice_number,
                        $invoice->customer->name,
                        'Rp ' . number_format($invoice->balance_due, 2),
                        Carbon::now()->diffInDays($invoice->due_date),
                        $invoice->status,
                    ];
                })
            );

            $this->info("Total overdue amount: Rp " . number_format($totalOverdueAmount, 2));

            // Send reminders
            $this->sendOverdueReminders($overdueInvoices);
        } else {
            $this->info('No overdue invoices found.');
        }

        return Command::SUCCESS;
    }

    /**
     * Send overdue reminders
     */
    private function sendOverdueReminders($invoices)
    {
        foreach ($invoices as $invoice) {
            // Update invoice status to overdue if not already
            if ($invoice->status !== 'overdue') {
                $invoice->update(['status' => 'overdue']);
            }

            // In a real application, send email to customer
            // Mail::to($invoice->customer->email)->send(new OverdueInvoiceReminder($invoice));
            
            Log::info("Overdue reminder sent for invoice {$invoice->invoice_number} to {$invoice->customer->email}");
        }

        // Notify administrators
        $admins = User::where('role', 'Administrator')->get();
        foreach ($admins as $admin) {
            Log::info("Overdue invoices summary sent to admin: {$admin->email}");
        }
    }
}