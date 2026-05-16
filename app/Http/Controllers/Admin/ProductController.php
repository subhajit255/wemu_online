<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Traits\UploadAble;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class ProductController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = Product::latest()->get();
        return view('admin.product.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            // dd($request->all());
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $request->validate([
                    'category_id' => 'required|exists:categories,id',
                    'title' => 'required|string',
                    'short_desc' => 'required|string',
                    'description' => 'required|string',
                    'additional_desc' => 'required|string',
                    'mrp' => 'required|numeric',
                    'price' => 'required|numeric',
                ]);
                $message = "Product Updated Successfully";
            } else {
                $request->validate([
                    'category_id' => 'required|exists:categories,id',
                    'title' => 'required|string',
                    'sku' => 'required|string',
                    'short_desc' => 'required|string',
                    'description' => 'required|string',
                    'additional_desc' => 'required|string',
                    'mrp' => 'required|numeric',
                    'price' => 'required|numeric',
                    'file.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                ]);
                $message = "Product Created Successfully";
            }

            DB::beginTransaction();
            try {
                $postData = [
                    "category_id" => $request->category_id,
                    "parent_id" => $this->getTopParent($request->category_id),
                    "title" => $request->title,
                    "slug" => $this->createUserName($request->title),
                    "sku" => $request->sku,
                    "short_desc" => $request->short_desc,
                    "description" => $request->description,
                    "additional_desc" => $request->additional_desc,
                    "mrp" => $request->mrp,
                    "price" => $request->price,
                ];
                $details = Product::updateOrCreate(['id' => $id], $postData);

                if (!empty($request->remove_image)) {
                    $remove_image = json_decode($request->remove_image);
                    ProductImage::whereIn('id', $remove_image)->delete();
                }

                if (!empty($request->file)) {
                    ProductImage::where('product_id', $details->id)->delete();
                    foreach ($request->file as $key => $val) {
                        $image = $val;
                        $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                        $isFileUploaded = $this->uploadOne($image, config('constants.SITE_PRODUCT_UPLOAD_PATH'), $fileName, 'public');
                        if ($isFileUploaded) {
                            ProductImage::create([
                                'product_id' => $details->id,
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
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.product.list')];
            return response($data);
        }
        $details = array();
        $categoryId = null;
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'products');
            $details = Product::find($uuid);
            $categoryId = $details->category_id;
        }
        $categories = Category::all();
        $categoriesOption = $this->categoryDropdownOptions($categories, $categoryId);
        return view('admin.product.add', compact('details', 'categoriesOption'));
    }
    public function productInventory(Request $request)
    {
        $details = array();
        $variants = array();
        $attributeCombination = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'products');
            $details = Product::find($uuid);
            $variants = ProductVariant::where('product_id', $uuid)->get();
            $attributeCombination = $this->getAllAttributesCombination($uuid);
        }
        if ($request->post()) {
            DB::beginTransaction();
            try {
                $combinations = $attributeCombination['combinations'];
                $dataToStore = array();
                foreach ($combinations as $key => $value) {
                    $dataToStore = array(
                        'product_id' => $uuid,
                        'name' => $request->name[$key],
                        'attribute_values' => json_encode($value),
                        'attribute_values_str' => implode('/', $value),
                        'price' => $request->price[$key],
                        'sku' => $request->sku[$key],
                        'qty' => $request->qty[$key],
                    );
                    $variantId = $variants[$key]->id ?? null;
                    ProductVariant::updateOrCreate(['id' => $variantId], $dataToStore);
                }
                DB::commit();
                $message = 'Inventory Saved Successfully';
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = $th->getMessage();
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.product.inventory', $request->uuid)];
            return response($data);
        }

        return view('admin.product.inventory', compact('details', 'attributeCombination', 'variants'));
    }
}
