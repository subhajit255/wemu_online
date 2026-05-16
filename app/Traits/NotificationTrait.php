<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

/**
 * Trait FlashMessages
 * @package App\Traits
 */

trait NotificationTrait
{
    public function saveNotification($data)
    {
        try {
            $notificationSave = Notification::create($data);
            return $notificationSave;
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
            return $this->responseJson($status, $code, $message, $response);
        }
    }
    public function addNotificationToAllCustomer($data)
    {
        try {
            $allActiveUser = User::where(['user_type' => 3, 'is_active' => 1])->get();
            $dataToStore = [];
            if (!empty($allActiveUser) && $allActiveUser->isNotEmpty()) {
                foreach ($allActiveUser as $key => $value) {
                    $dataToStore[] = [
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        'user_id' => $value->id,
                        'title' => $data['title'],
                        'description' => $data['description'],
                        'is_read' => 0,
                        'for' => 2,
                        'type' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
                $notificationSave = Notification::insert($dataToStore);
                if($notificationSave){
                    return true;
                }
                return false;
            }
            return false;
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
            return $this->responseJson($status, $code, $message, $response);
        }
    }

    public function getAllNotifications()
    {
        try {
            $allNotification = Notification::where('user_id', Auth::user()->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->latest()
                ->get();
            if ($allNotification) {
                return $allNotification;
            } else {
                return array();
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
            return $this->responseJson($status, $code, $message, $response);
        }
    }

    public function countUnReadNotification()
    {
        try {
            $allNotificationCount = Notification::where(['user_id' => Auth::user()->id, 'is_read' => 0])->count();
            if ($allNotificationCount) {
                return $allNotificationCount;
            } else {
                return 0;
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
            return $this->responseJson($status, $code, $message, $response);
        }
    }
}
