<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\UserExpense;
use Illuminate\Console\Command;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Log;
use App\Traits\PushNotificationTrait;

class ExpenseRemainderCron extends Command
{
    use NotificationTrait;
    use PushNotificationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense-remainder:cron';

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
            Log::info('Expense Remainder Cron Started on ' . Carbon::now()->format('Y-m-d') . ' ' . Carbon::now()->format('H:i'));
            // Log::info(Carbon::now()->format('H:i'));
            $userExpense = UserExpense::whereDate('remainder_date', Carbon::now()->format('Y-m-d'))->where('remainder_time', Carbon::now()->format('H:i'))->where('is_active', 1)->get();
            // Log::info($userExpense);
            if (!empty($userExpense)) {
                foreach ($userExpense as $expense) {
                    $title = 'Remainder for expense ' . $expense->title;
                    $body = 'You have an expense of ' . $expense->amount . ' for ' . $expense->title . ' on ' . $expense->remainder_date . ' at ' . $expense->remainder_time;
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
            Log::error('Expense Remainder Error ' . $e->getMessage());
        }
    }
}


