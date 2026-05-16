<?php

namespace App\Console\Commands;

use App\Models\UserGoalTask;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Log;
use App\Traits\PushNotificationTrait;

class TaskRemainderCron extends Command
{
    use NotificationTrait;
    use PushNotificationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task-remainder:cron';

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
            Log::info('Task Remainder Cron Started on ' . Carbon::now()->format('Y-m-d') . ' ' . Carbon::now()->format('H:i'));
            $userTask = UserGoalTask::whereNotNull('service_frequency_id')->where('start_date', '<=', Carbon::now()->format('Y-m-d'))->where('end_date', '>=', Carbon::now()->format('Y-m-d'))->where('is_active', 1)->get();

            if (!empty($userTask)) {
                foreach ($userTask as $task) {
                    // Log::info('Task Remainder Cron for Task ' . $task);
                    $dayCountTillDate = Carbon::now()->diffInDays(Carbon::parse($task->start_date));
                    $remainderDayGap = $task->serviceFrequency->day_count ?? null;
                    // Log::info('Day Count till date ' . $dayCountTillDate);
                    // Log::info('Remainder Day Gap ' . $remainderDayGap);
                    if (fmod($dayCountTillDate, $remainderDayGap) == 0) {
                        $title = 'Remainder for task ' . $task->title;
                        $body = 'You have a task ' . $task->title . ' which is due on ' . Carbon::parse($task->end_date)->format('d M Y') . '. Please make sure to complete it on time.';
                        $this->saveNotification([
                            'user_id' => $task->goal->user_id,
                            'title' => $title,
                            'description' => $body,
                            'type' => 'task',
                            'for' => 2,
                            'is_read' => 0
                        ]);
                        $this->sendPushNotification($task->goal->user_id, $title, $body, 'task');
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Goal Remainder Error ' . $e->getMessage());
        }
    }
}


