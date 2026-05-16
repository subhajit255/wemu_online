<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Setting;
use App\Models\UserPlan;
use App\Traits\UploadAble;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use App\Traits\PushNotificationTrait;
use App\Http\Controllers\BaseController;

class TransactionController extends BaseController
{
    use NotificationTrait;
    use PushNotificationTrait;
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', '!=', null)->latest();
        if ($request->search) {
            $query->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
                $query->orWhere('mobile_number', $request->search);
                $query->orWhere('email', $request->search);
            });
        }
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        if ($request->daterange) {
            $dateRange = explode(' - ', $request->daterange);
            $startDate = Carbon::parse($dateRange[0]);
            $endDate = Carbon::parse($dateRange[1]);
            // dd($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
            $query->whereDate('created_at', '>=', $startDate->format('Y-m-d'));
            $query->whereDate('created_at', '<=', $endDate->format('Y-m-d'));
        } else {
            $query->whereDate('created_at', '>=', $startDate->format('Y-m-d'));
            $query->whereDate('created_at', '<=', $endDate->format('Y-m-d'));
        }
        if($request->payment_status_val){
            $query->where('payment_status', $request->payment_status_val);
        }
        $startDate = Carbon::parse($startDate)->format('m/d/Y');
        $endDate = Carbon::parse($endDate)->format('m/d/Y');
        $details = $query->paginate($request->pagination ?? 25);
        // $details = $query->toRawSql();
        // dd($details);
        return view('admin.transaction.index', compact('details', 'startDate', 'endDate'));
    }
    public function details(Request $request)
    {
        $detail = Transaction::where('uuid', $request->uuid)->first();
        return view('admin.transaction.details', compact('detail'));
    }
    public function invoice(Request $request)
    {
        $detail = Transaction::where('uuid', $request->uuid)->first();
        $setting = Setting::first();
        // $options = new Options();
        // $options->set('isRemoteEnabled', true);
        // $domPdf = new Dompdf($options);
        return view('admin.transaction.invoice', compact('detail', 'setting'));
        // $html = view('admin.transaction.invoice', compact('detail', 'setting'));
        // $domPdf->loadHtml($html);
        // $domPdf->setPaper('A4', 'portrait');
        // $domPdf->render();
        // $domPdf->stream();
    }
}
