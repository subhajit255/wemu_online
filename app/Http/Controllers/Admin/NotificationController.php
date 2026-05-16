<?php

namespace App\Http\Controllers\Admin;

use App\Traits\UploadAble;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Http\Controllers\BaseController;

class NotificationController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = Notification::where('for', 1)->latest()->paginate(10);
        return view('admin.notification.index', compact('details'));
    }
}
