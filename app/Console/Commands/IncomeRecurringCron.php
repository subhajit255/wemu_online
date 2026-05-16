<?php

namespace App\Console\Commands;

use App\Models\UserIncome;
use App\Traits\NotificationTrait;
use App\Traits\PushNotificationTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class IncomeRecurringCron extends Command
{
    use NotificationTrait;
    use PushNotificationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'income-recurring:cron';

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
            Log::info('Income Recurring Cron Started on ' . Carbon::now()->format('Y-m-d') . ' ' . Carbon::now()->format('H:i'));
            $previousMonthSameDay = Carbon::now()->subMonth()->format('Y-m-d');
            $userIncome = UserIncome::where(['is_recurring' => 1, 'is_active' => 1])->whereDate('date', $previousMonthSameDay)->get();
            // Log::info($userIncome);
            if (!empty($userIncome) && $userIncome->isNotEmpty()) {
                foreach ($userIncome as $income) {
                    UserIncome::create([
                        'date' => Carbon::now()->format('Y-m-d'),
                        'name' => $income->name,
                        'amount' => $income->amount,
                        'is_recurring' => 1,
                        'user_id' => $income->user_id,
                    ]);

                    $title = $income->name . ' (Recurring)';
                    $body = $income->name . ' has been added to your account';
                    $this->saveNotification([
                        'user_id' => $income->user_id,
                        'title' => $title,
                        'description' => $body,
                        'type' => 'income',
                        'for' => 2,
                        'is_read' => 0
                    ]);
                    $this->sendPushNotification($income->user_id, $title, $body, 'income');
                }
            }
        } catch (\Exception $e) {
            Log::error('Income Recurring Error ' . $e->getMessage());
        }
    }
}


