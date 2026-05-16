<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Attribute;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class AttributeController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = Attribute::latest()->get();
        return view('admin.attribute.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;

            if (!empty($id)) {
                $message = "Attribute Updated Successfully";
            } else {
                $message = "Attribute Created Successfully";
            }

            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string',
                "values"    => "required|array|min:2",
                "values.*"  => "required|string",
            ]);

            DB::beginTransaction();
            try {
                $postData = [
                    "name" => $request->name,
                    "category_id" => $request->category_id,
                ];
                $details = Attribute::updateOrCreate(['id' => $id], $postData);
                if (!empty($details)) {
                    $attributeId = $details->id;
                    AttributeValue::where('attribute_id', $attributeId)->delete();
                    foreach ($request->values as $value) {
                        $valuesStore = array(
                            'attribute_id' => $attributeId,
                            'value' => $value
                        );
                        AttributeValue::create($valuesStore);
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
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.attribute.list')];
            return response($data);
        }
        $details = array();
        $categoryId = null;
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'attributes');
            $details = Attribute::find($uuid);
            $categoryId = $details->category_id;
        }

        $categories = Category::where('parent_id', NULL)->get();
        return view('admin.attribute.add', compact('details', 'categories'));
    }
}
