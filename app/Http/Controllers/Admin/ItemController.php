<?php

namespace App\Http\Controllers\Admin;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\ItemImage;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Models\ServiceFrequency;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class ItemController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $query = Item::latest();
        if (!empty($request->search)) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('model_no', 'like', "%{$request->search}%")
                ->orWhere('serial_no', 'like', "%{$request->search}%")
                ->orWhereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('mobile_number', $request->search)
                        ->orWhere('email', $request->search);
                })->orWhereHas('category', function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%");
                });
        }
        $details = $query->paginate(20);
        // $details = $query->toRawSql();
        // dd($details);
        return view('admin.item.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $message = "Item Updated Successfully";
            } else {
                $message = "Item Created Successfully";
            }

            $request->validate([
                'uuid' => 'nullable|exists:items,uuid',
                'user_id' => 'required|exists:users,uuid',
                'category_id' => 'required|exists:categories,uuid',
                'name' => 'required',
                'price' => 'required|numeric',
                'date' => 'required|date',
                'brand_name' => 'nullable|string',
                'model_no' => 'nullable|string',
                'serial_no' => 'nullable|string',
                'is_expense' => 'required|in:0,1',
                'supplier_name' => 'nullable|string',
                'supplier_contact_number' => 'nullable|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',

                'service_frequency_id' => 'nullable|exists:service_frequencies,uuid',
                'last_service_date' => 'nullable|date',
                'comment_service' => 'nullable|string',
                'set_remainder_service' => 'nullable|in:0,1',

                'expiry_date' => 'date|after_or_equal:today',
                'set_remainder_warranty' => 'required|in:0,1',
                'include' => 'required|in:1,2,3,4',
                'customer_care_number' => 'required|numeric|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'comment_warranty' => 'required|string',
                'file.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            DB::beginTransaction();
            try {
                $itemAdded = Item::updateOrCreate(['uuid' => $request->uuid ?? null], [
                    'user_id' => uuidtoid($request->user_id, 'users'),
                    'category_id' => uuidtoid($request->category_id, 'categories'),
                    'name' => $request->name,
                    'price' => $request->price,
                    'date' => $request->date,
                    'brand_name' => $request->brand_name,
                    'model_no' => $request->model_no,
                    'serial_no' => $request->serial_no,
                    'is_expense' => $request->is_expense,
                    'supplier_name' => $request->supplier_name ?? null,
                    'supplier_contact_number' => $request->supplier_contact_number ?? null,
                ]);

                $itemAdded->serviceDetail()->updateOrCreate([
                    'item_id' => $itemAdded->id,
                ], [
                    'service_frequency_id' => uuidtoid($request->service_frequency_id, 'service_frequencies'),
                    'last_service_date' => $request->last_service_date,
                    'comments' => $request->comment_service,
                    'set_remainder' => $request->set_remainder_service,
                ]);

                $itemAdded->warrantyDetail()->updateOrCreate([
                    'item_id' => $itemAdded->id,
                ], [
                    'expiry_date' => $request->expiry_date,
                    'set_remainder' => $request->set_remainder_warranty,
                    'include' => $request->include,
                    'customer_care_number' => $request->customer_care_number,
                    'comments' => $request->comment_warranty,
                ]);

                $itemId = $id ?? $itemAdded->id;

                if (!empty($request->remove_image)) {
                    $remove_image = json_decode($request->remove_image);
                    ItemImage::whereIn('id', $remove_image)->delete();
                }

                if (!empty($request->file)) {
                    // ItemImage::where('item_id', $itemId)->delete();
                    foreach ($request->file as $key => $val) {
                        $image = $val;
                        $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                        $isFileUploaded = $this->uploadOne($image, config('constants.SITE_ITEM_UPLOAD_PATH'), $fileName, 'public');
                        if ($isFileUploaded) {
                            ItemImage::create([
                                'item_id' => $itemId,
                                'image' => $fileName,
                                'is_active' => 1
                            ]);
                        }
                    }
                }

                DB::Commit();

            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = $th->getMessage();
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.item.list')];
            return response($data);
        }
        $details = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'items');
            $details = Item::find($uuid);
        }
        $users = User::where('user_type', 3)->get();
        $categories = Category::where('is_active', 1)->get();
        $serviceFrequencies = ServiceFrequency::where('is_active', 1)->get();
        return view('admin.item.add', compact('details', 'users', 'categories', 'serviceFrequencies'));
    }

    public function view(Request $request)
    {
        $details = Item::where('uuid', $request->uuid)->first();
        return view('admin.item.view', compact('details'));
    }
}
