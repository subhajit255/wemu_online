<?php

namespace App\Console\Commands;

use App\Models\UserExpense;
use App\Traits\NotificationTrait;
use App\Traits\PushNotificationTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpenseRecurringCron extends Command
{
    use NotificationTrait;
    use PushNotificationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense-recurring:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            Log::info('Expense Recurring Cron Started on ' . Carbon::now()->format('Y-m-d') . ' ' . Carbon::now()->format('H:i'));
            $previousMonthSameDay = Carbon::now()->subMonth()->format('Y-m-d');
            $userExpense = UserExpense::where(['is_recurring' => 1, 'is_active' => 1])->whereDate('date', $previousMonthSameDay)->get();
            // Log::info($userExpense);
            if (!empty($userExpense) && $userExpense->isNotEmpty()) {
                foreach ($userExpense as $expense) {
                    UserExpense::create([
                        'user_id' => $expense->user_id,
                        'category_id' => $expense->category_id,
                        'service_frequency_id' => $expense->service_frequency_id,
                        'name' => $expense->name,
                        'price' => $expense->price,
                        'e_script_url' => $expense->e_script_url,
                        'is_tax_deductible' => $expense->is_tax_deductible,
                        'remainder_date' => $expense->remainder_date,
                        'remainder_time' => $expense->remainder_time,
                        'date' => Carbon::now()->format('Y-m-d'),
                        'is_recurring' => 1,
                    ]);

                    $title = $expense->name . ' (Recurring)';
                    $body = $expense->name . ' has been added to your account';
                    $this->saveNotification([
                        'user_id' => $expense->user_id,
                        'title' => $title,
                        'description' => $body,
                        'type' => 'expense',
                        'for' => 2,
                        'is_read' => 0
                    ]);
                    $this->sendPushNotification($expense->user_id, $title, $body, 'expense');
                }
            }
        } catch (\Exception $e) {
            Log::error('Expense Recurring Error ' . $e->getMessage());
        }
    }
}


