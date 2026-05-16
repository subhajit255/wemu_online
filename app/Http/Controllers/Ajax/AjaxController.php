<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Cms;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\Item;
use App\Models\News;
use App\Models\User;
use App\Models\Banner;
use App\Models\Feature;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Broadcast;
use App\Models\UserIncome;
use App\Models\ProductImage;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\ServiceFrequency;
use App\Http\Controllers\BaseController;

class AjaxController extends BaseController
{
    public function deleteData(Request $request)
    {
        if ($request->ajax()) {
            $table = $request->find;
            switch ($table) {
                case 'users':
                    $id = uuidtoid($request->uuid, $table);
                    $data = User::find($id);
                    $data->forceDelete();
                    $message = 'User Deleted';
                    break;
                case 'banners':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Banner::find($id);
                    $data->delete();
                    $message = 'Banner Deleted';
                    break;
                case 'news':
                    $id = uuidtoid($request->uuid, $table);
                    $data = News::find($id);
                    $data->delete();
                    $message = 'News Deleted';
                    break;
                case 'cms':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Cms::find($id);
                    $data->delete();
                    $message = 'Cms Deleted';
                    break;
                case 'categories':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Category::find($id);
                    $data->delete();
                    $message = 'Category Deleted';
                    break;
                case 'attributes':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Attribute::find($id);
                    $data->delete();
                    $message = 'Attribute Deleted';
                    break;
                case 'products':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Product::find($id);
                    $data->delete();
                    $message = 'Product Deleted';
                    break;
                case 'product_images':
                    $id = $request->uuid;
                    $data = ProductImage::find($id);
                    $data->delete();
                    $message = 'Product Images Deleted';
                    break;
                case 'service_frequencies':
                    $id = uuidtoid($request->uuid, $table);
                    $data = ServiceFrequency::find($id);
                    $data->delete();
                    $message = 'Service Frequency Deleted';
                    break;
                case 'items':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Item::find($id);
                    $data->delete();
                    $message = 'Item Deleted';
                    break;
                case 'user_incomes':
                    $id = uuidtoid($request->uuid, $table);
                    $data = UserIncome::find($id);
                    $data->delete();
                    $message = 'Income Deleted';
                    break;
                case 'subscriptions':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Subscription::find($id);
                    if (isActiveSubscription($id)) {
                        $data->delete();
                        $message = 'Subscription Deleted...';
                    } else {
                        $message = 'Subscription is active not Possible to delete';
                    }
                    break;
                case 'broadcasts':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Broadcast::find($id);
                    $data->delete();
                    $message = 'Broadcast Deleted';
                    break;
                case 'faqs':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Faq::find($id);
                    $data->delete();
                    $message = 'FAQ Deleted';
                    break;
                case 'coupons':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Coupon::find($id);
                    $data->delete();
                    $message = 'Coupon Deleted';
                    break;
            }
            if ($data) {
                return $this->responseJson(true, 200, $message);
            } else {
                return $this->responseJson(false, 200, 'Something Went Wrong');
            }
        } else {
            abort(403);
        }
    }
    public function statusChange(Request $request)
    {
        // dd(1);
        if ($request->ajax()) {
            $table = $request->find;
            $message = 'Status changed successfully';
            switch ($table) {
                case 'users':
                    $id = uuidtoid($request->uuid, $table);
                    $data = User::find($id);
                    $data->update(['is_active' => $request->status]);
                    break;
                case 'features':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Feature::find($id);
                    $data->update(['is_active' => $request->status]);
                    break;
                case 'cms':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Cms::find($id);
                    $data->update(['is_active' => $request->status]);
                    break;
                case 'categories':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Category::find($id);
                    $data->update(['is_active' => $request->status]);
                    break;
                case 'service_frequencies':
                    $id = uuidtoid($request->uuid, $table);
                    $data = ServiceFrequency::find($id);
                    $data->update(['is_active' => $request->status]);
                    break;
                case 'subscriptions':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Subscription::find($id);
                    $data->update(['is_active' => $request->status]);
                    break;
                case 'broadcasts':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Broadcast::find($id);
                    $data->update(['is_active' => $request->status]);
                    break;
                case 'faqs':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Faq::find($id);
                    $data->update(['is_active' => $request->status]);
                    break;
                case 'banners':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Banner::find($id);
                    $data->update(['is_active' => $request->status]);
                    break;
                case 'coupons':
                    $id = uuidtoid($request->uuid, $table);
                    $data = Coupon::find($id);
                    $data->update(['is_active' => $request->status]);
                    break;
            }
            if ($data) {
                return $this->responseJson(true, 200, $message);
            } else {
                return $this->responseJson(false, 200, 'Something Went Wrong');
            }
        } else {
            abort(403);
        }
    }
}
