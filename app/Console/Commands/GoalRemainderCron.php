<?php

namespace App\Console\Commands;

use App\Models\UserGoal;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Log;
use App\Traits\PushNotificationTrait;

class GoalRemainderCron extends Command
{
    use NotificationTrait;
    use PushNotificationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goal-remainder:cron';

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
            Log::info('Goal Remainder Cron Started on ' . Carbon::now()->format('Y-m-d') . ' ' . Carbon::now()->format('H:i'));
            $userGoal = UserGoal::whereNotNull('service_frequency_id')->where('start_date', '<=', Carbon::now()->format('Y-m-d'))->where('end_date', '>=', Carbon::now()->format('Y-m-d'))->where('is_active', 1)->get();

            if (!empty($userGoal)) {
                foreach ($userGoal as $goal) {
                    // Log::info('Goal Remainder Cron for Goal ' . $goal);
                    $dayCountTillDate = Carbon::now()->diffInDays(Carbon::parse($goal->start_date));
                    $remainderDayGap = $goal->serviceFrequency->day_count ?? null;
                    // Log::info('Day Count till date ' . $dayCountTillDate);
                    // Log::info('Remainder Day Gap ' . $remainderDayGap);
                    if (fmod($dayCountTillDate, $remainderDayGap) == 0) {
                        $title = 'Remainder for goal ' . $goal->title;
                        $body = 'You have a goal ' . $goal->title . ' which is due on ' . Carbon::parse($goal->end_date)->format('d M Y') . '. Please make sure to complete it on time.';
                        $this->saveNotification([
                            'user_id' => $goal->user_id,
                            'title' => $title,
                            'description' => $body,
                            'type' => 'goal',
                            'for' => 2,
                            'is_read' => 0
                        ]);
                        $this->sendPushNotification($goal->user_id, $title, $body, 'goal');
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Goal Remainder Error ' . $e->getMessage());
        }
    }
}


