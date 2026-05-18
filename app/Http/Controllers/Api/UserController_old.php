<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\Banner;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\UserGoal;
use Stripe\StripeClient;
use App\Models\ItemImage;
use App\Models\UserBudget;
use App\Models\UserIncome;
use App\Traits\UploadAble;
use App\Models\Transaction;
use App\Models\UserExpense;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\UserGoalTask;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Models\FavouriteProduct;
use App\Models\UserSubscription;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\PushNotificationTrait;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\User\ItemCollection;
use App\Http\Resources\Api\User\BannerCollection;
use App\Http\Resources\Api\User\CouponCollection;
use App\Http\Resources\Api\User\ProfileCollection;
use App\Http\Resources\Api\User\UserGoalCollection;
use App\Http\Resources\Api\User\UserIncomeCollection;
use App\Http\Resources\Api\User\UserExpenseCollection;
use App\Http\Resources\Api\User\NotificationCollection;
use App\Http\Resources\Api\User\PaginateItemCollection;
use App\Http\Resources\Api\User\SubscriptionCollection;
use App\Models\BudgetRecurring;
use App\Models\ExpanseRecurring;
use App\Models\IncomeRecurring;

class UserController_old extends BaseController
{
    use CommonFunction;
    use UploadAble;
    use NotificationTrait;
    use PushNotificationTrait;

    public function userList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        // dd($userFound->toArray());
        $query = User::where(['user_type' => $request->user_type, 'is_approve' => 1, 'is_blocked' => 0]);

        if ($request->has('name')) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->name . '%');
            });
        }
        if ($request->is_verified != '') {
            $query = $query->where('is_verified', (int) $request->is_verified);
        }
        if (!empty($request->state)) {
            $query = $query->where('state', $request->state);
        }
        if (!empty($request->city)) {
            $query = $query->where('city', $request->city);
        }

        $userDetails = $query->get();

        if (is_null($userDetails->toArray())) {
            $status = false;
            $code = 200;
            $response = [];
            $message = 'No Data Found';
            return $this->responseJson($status, $code, $message, $response);
        }

        if ($userDetails) {
            $status = true;
            $code = 200;
            $response = ProfileCollection::collection($userDetails);
            $message = "User Found";
            return $this->responseJson($status, $code, $message, $response);
        } else {
            $status = false;
            $code = 422;
            $response = [];
            $message = 'Something went wrong.';
            return $this->responseJson($status, $code, $message, $response);
        }
    }
    public function profileDetails(Request $request)
    {
        try {
            $userDetails = Auth::user();
            $userExpense = UserExpense::where('user_id', $userDetails->id)->orderBy('id', 'DESC')->limit(5)->get();
            if ($userDetails) {
                $status = true;
                $code = 200;
                $response = [
                    'profile' => new ProfileCollection($userDetails),
                    'recent_expenses' => UserExpenseCollection::collection($userExpense)
                ];
                $message = "Data Found";
                return $this->responseJson($status, $code, $message, $response);
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
                return $this->responseJson($status, $code, $message, $response);
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
            return $this->responseJson($status, $code, $message, $response);
        }
    }
    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;
        $rules = [
            "email" => 'sometimes|email|unique:users,email,' . $id,
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $data_to_update = array();

            if (!empty($request->name)) {
                $data_to_update['name'] = $request->name;
            }
            if (!empty($request->email)) {
                $data_to_update['email'] = $request->email;
            }
            if (!empty($request->lat)) {
                $data_to_update['lat'] = $request->lat;
            }
            if (!empty($request->lng)) {
                $data_to_update['lng'] = $request->lng;
            }

            if (!empty($request->profile_image)) {
                $image = $request->profile_image;
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $isFileUploaded = $this->uploadOne($image, config('constants.SITE_PROFILE_IMAGE_UPLOAD_PATH'), $fileName, 'public');
                if ($isFileUploaded) {
                    $data_to_update['profile_image'] = $fileName;
                }
            }

            $userUpdated = User::where('id', $id)->update($data_to_update);

            if ($userUpdated) {
                DB::commit();
                $userDetails = User::where('id', $id)->first();
                $status = true;
                $code = 200;
                $response = new ProfileCollection($userDetails);
                $message = "Profile updated successfully";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
            return $this->responseJson($status, $code, $message, $response);
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userIncomeAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'nullable|exists:user_incomes,uuid',
            'service_frequency_id' => 'nullable|exists:service_frequencies,uuid',
            'amount' => 'required',
            'date' => 'required|date',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $userDetails = Auth::user();
            $service_frequency_id = !empty($request->service_frequency_id) ? uuidtoid($request->service_frequency_id, 'service_frequencies') : null;
            $data = [
                'user_id' => $userDetails->id,
                'service_frequency_id' => $service_frequency_id,
                'name' => $request->name,
                'amount' => $request->amount,
                'is_recurring' => $request->is_recurring ?? 0,
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
                'is_active' => 1,
            ];
            $userIncome = $userDetails->userIncome()->updateOrCreate(['uuid' => $request->uuid ?? null], $data);
            if ($userIncome) {

                if ($request->is_recurring) {
                    $incomeRecurring = IncomeRecurring::where('income_id', $userIncome->id)->first();
                    if (!$incomeRecurring) {
                        IncomeRecurring::create([
                            'income_id' => $userIncome->id,
                            'starting_date' => date('Y-m-d'),
                            'ending_date' => null
                        ]);
                    }
                }


                userSubscriptionActivity();
                //notification
                // $title = 'Income Added Successfully';
                // $body = 'Amount is ' . $request->amount;
                // $this->saveNotification([
                //     'user_id' => auth()->user()->id,
                //     'title' => $title,
                //     'description' => $body,
                //     'type' => 'income',
                //     'for' => 2,
                //     'is_read' => 0
                // ]);
                // $this->sendPushNotificationOnToken(auth()->user()->fcm_token, $title, $body, 'income');
                DB::commit();
                //notification
                $status = true;
                $code = 200;
                $response = [];
                $message = "Income added successfully";
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "Something went wrong";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
            return $this->responseJson($status, $code, $message, $response);
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userIncomeRecurringStop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'income_id' => 'required|exists:user_incomes,id',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $incomeId = $request->income_id;
            $income = UserIncome::find($incomeId);
            if ($income) {
                $incomeRecurring = IncomeRecurring::where('income_id', $incomeId)->first();
                if ($incomeRecurring) {
                    $incomeRecurring->update(['ending_date' => date('Y-m-d')]);
                }
                // $income->update(['is_recurring' => 0]);
                DB::commit();
                $status = true;
                $code = 200;
                $response = [];
                $message = "Income recurring stopped successfully";
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "Income not found";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userItemAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'nullable|exists:items,uuid',
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
            'comment_warranty' => 'nullable|string',
            'bill' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $service_frequency_id = null;
            if (!empty($request->service_frequency_id)) {
                $service_frequency_id = uuidtoid($request->service_frequency_id, 'service_frequencies');
            }

            $postData = [
                'user_id' => auth()->user()->id,
                'category_id' => uuidtoid($request->category_id, 'categories'),
                'name' => $request->name,
                'price' => $request->price,
                'date' => $request->date,
                'brand_name' => $request->brand_name,
                'model_no' => $request->model_no ?? null,
                'serial_no' => $request->serial_no ?? null,
                'is_expense' => $request->is_expense,
                'supplier_name' => $request->supplier_name ?? null,
                'supplier_contact_number' => $request->supplier_contact_number ?? null,
            ];

            if (!empty($request->bill)) {
                $image = $request->bill;
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $isFileUploaded = $this->uploadOne($image, config('constants.SITE_ITEM_UPLOAD_PATH'), $fileName, 'public');
                if ($isFileUploaded) {
                    $postData['bill'] = $fileName;
                }
            }

            $itemAdded = Item::updateOrCreate(['uuid' => $request->uuid ?? null], $postData);

            $itemAdded->serviceDetail()->updateOrCreate([
                'item_id' => $itemAdded->id,
            ], [
                'service_frequency_id' => $service_frequency_id,
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

            // if (!empty($request->remove_image)) {
            //     $remove_image = json_decode($request->remove_image);
            //     ItemImage::whereIn('id', $remove_image)->delete();
            // }

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

            if ($itemAdded) {
                userSubscriptionActivity(); // for subscription activity
                // $title = 'Item Added Successfully';
                // $body = 'Item name is ' . $request->name . ' and price is ' . $request->price;
                // $this->saveNotification([
                //     'user_id' => auth()->user()->id,
                //     'title' => $title,
                //     'description' => $body,
                //     'type' => 'item',
                //     'for' => 2,
                //     'is_read' => 0
                // ]);
                // $this->sendPushNotificationOnToken(auth()->user()->fcm_token, $title, $body, 'item');
                DB::commit();
                $status = true;
                $code = 200;
                $response = [];
                $message = "Item added successfully";
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "Something went wrong";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
            return $this->responseJson($status, $code, $message, $response);
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userExpenseAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'nullable|exists:user_expenses,uuid',
            'name' => 'required|string',
            'supplier' => 'nullable|string',
            'price' => 'required|numeric',
            'date' => 'required|date',
            'is_recurring' => 'required|in:0,1',
            'is_tax_deductible' => 'required|in:0,1',
            'category_id' => 'required|exists:categories,uuid',
            'service_frequency_id' => 'nullable|exists:service_frequencies,uuid',
            'bill' => 'nullable|image',
            'e_script_url' => 'nullable|string',
            'remainder_date' => 'nullable|date',
            'remainder_time' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();

        try {
            $serviceFrequencyId = !empty($request->service_frequency_id) ? uuidtoid($request->service_frequency_id, 'service_frequencies') : NULL;
            $postData = [
                "user_id" => auth()->user()->id,
                "category_id" => uuidtoid($request->category_id, 'categories'),
                "service_frequency_id" => $serviceFrequencyId,
                "name" => $request->name,
                "supplier" => $request->supplier,
                "price" => $request->price,
                "date" => $request->date,
                "e_script_url" => $request->e_script_url ?? null,
                "is_recurring" => $request->is_recurring ?? 0,
                "is_tax_deductible" => $request->is_tax_deductible ?? 0,
                "remainder_date" => $request->remainder_date ?? null,
                "remainder_time" => $request->remainder_time ?? null,
            ];

            if (!empty($request->bill)) {
                $image = $request->bill;
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $isFileUploaded = $this->uploadOne($image, config('constants.SITE_BILL_UPLOAD_PATH'), $fileName, 'public');
                if ($isFileUploaded) {
                    $postData['bill'] = $fileName;
                }
            }

            $expense = UserExpense::updateOrCreate(['uuid' => $request->uuid], $postData);
            if ($expense) {
                if ($request->is_recurring == 1) {
                    $expanseRecurring = ExpanseRecurring::where('expanse_id', $expense->id)->first();
                    if (empty($expanseRecurring)) {
                        ExpanseRecurring::create([
                            'expanse_id' => $expense->id,
                            'starting_date' => date('Y-m-d'),
                            'ending_date' => null,
                        ]);
                    }
                }

                userSubscriptionActivity(); //for update subscription
                // $title = 'Expense Added Successfully';
                // $body = 'Expense name is ' . $request->name . ' and price is ' . $request->price;
                // $this->saveNotification([
                //     'user_id' => auth()->user()->id,
                //     'title' => $title,
                //     'description' => $body,
                //     'type' => 'expense',
                //     'for' => 2,
                //     'is_read' => 0
                // ]);
                // $this->sendPushNotificationOnToken(auth()->user()->fcm_token, $title, $body, 'expense');
                DB::commit();
                $status = true;
                $code = 200;
                $response = [];
                $message = "Expense added successfully";
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "Something went wrong";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userExpenseRecurringStop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_id' => 'required|exists:user_expenses,id',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $expenseId = $request->expense_id;
            $expense = UserExpense::find($expenseId);
            if ($expense) {
                $expenseRecurring = ExpanseRecurring::where('expanse_id', $expenseId)->first();
                if ($expenseRecurring) {
                    $expenseRecurring->update(['ending_date' => date('Y-m-d')]);
                }
                DB::commit();
                $status = true;
                $code = 200;
                $response = [];
                $message = "Expense recurring stopped successfully";
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "Expense not found";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userBudgetAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'nullable|exists:user_budgets,uuid',
            'year' => 'required|numeric|digits:4',
            'month' => 'required|numeric|digits:2',
            'category_id' => 'required|exists:categories,uuid',
            'budget' => 'required|numeric',
            'is_recurring' => 'required|in:0,1',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();

        try {
            $categoryId = uuidtoid($request->category_id, 'categories');
            $postData = [
                "user_id" => auth()->user()->id,
                "category_id" => $categoryId,
                "budget" => $request->budget,
                "year" => $request->year,
                "month" => $request->month,
                "is_recurring" => $request->is_recurring ?? 0,
            ];

            $budget = UserBudget::updateOrCreate(['uuid' => $request->uuid], $postData);
            if ($budget) {
                if ($request->is_recurring == 1) {
                    $budgetRecurring = BudgetRecurring::where('budget_id', $budget->id)->first();
                    if (empty($budgetRecurring)) {
                        BudgetRecurring::create([
                            'budget_id' => $budget->id,
                            'starting_date' => date('Y-m-d'),
                            'ending_date' => null,
                        ]);
                    }
                }
                // $category = Category::find($categoryId);
                // $title = 'Budget Added Successfully';
                // $body = $category->title . ' Budget is ' . $request->budget;
                // $this->saveNotification([
                //     'user_id' => auth()->user()->id,
                //     'title' => $title,
                //     'description' => $body,
                //     'type' => 'budget',
                //     'for' => 2,
                //     'is_read' => 0
                // ]);
                // $this->sendPushNotificationOnToken(auth()->user()->fcm_token, $title, $body, 'budget');
                userSubscriptionActivity();
                DB::commit();
                $status = true;
                $code = 200;
                $response = [];
                $message = "Budget added successfully";
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "Something went wrong";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userBudgetRecurringStop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budget_id' => 'required|exists:user_budgets,id',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $budgetId = $request->budget_id;
            $budget = UserBudget::find($budgetId);
            if ($budget) {
                $budgetRecurring = BudgetRecurring::where('budget_id', $budgetId)->first();
                if ($budgetRecurring) {
                    $budgetRecurring->update(['ending_date' => date('Y-m-d')]);
                }
                // $budget->update(['is_recurring' => 0]);
                DB::commit();
                $status = true;
                $code = 200;
                $response = [];
                $message = "Budget recurring stopped successfully";
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "Budget not found";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userGoalAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'nullable|exists:user_goals,uuid',
            'service_frequency_id' => 'nullable|exists:service_frequencies,uuid',
            'name' => 'required|string',
            'image' => 'nullable|image',
            'link' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'comments' => 'nullable|string',
            'url' => 'nullable|string',
            'task' => 'nullable|array',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $serviceFrequencyId = !empty($request->service_frequency_id) ? uuidtoid($request->service_frequency_id, 'service_frequencies') : NULL;
            $postData = [
                "user_id" => auth()->user()->id,
                "service_frequency_id" => $serviceFrequencyId,
                "name" => $request->name,
                "link" => $request->link,
                "start_date" => $request->start_date,
                "end_date" => $request->end_date,
                'comments' => $request->comments,
                'url' => $request->url,
                "is_completed" => 0,
            ];

            if (!empty($request->image)) {
                $image = $request->image;
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $isFileUploaded = $this->uploadOne($image, config('constants.SITE_GOAL_UPLOAD_PATH'), $fileName, 'public');
                if ($isFileUploaded) {
                    $postData['image'] = $fileName;
                }
            }

            $userGoalAddOrUpdate = UserGoal::updateOrCreate(['uuid' => $request->uuid], $postData);
            $userGoal = (!empty($request->uuid)) ? UserGoal::where('uuid', $request->uuid)->first() : $userGoalAddOrUpdate;

            if ($request->task) {
                $userGoal->tasks()->forceDelete();
                foreach ($request->task as $task) {
                    $userGoal->tasks()->updateOrCreate(['uuid' => $task['uuid']], [
                        'name' => $task['name'],
                        'task_type' => $task['task_type'] ?? 1,
                        // 'start_date' => $task['start_date'],
                        // 'end_date' => $task['end_date'],
                        'comments' => $task['comments'] ?? null,
                        'url' => $task['url'] ?? null,
                        'service_frequency_id' => !empty($task['service_frequency_id']) ? uuidtoid($task['service_frequency_id'], 'service_frequencies') : NULL,
                        'is_completed' => $task['is_completed'] ?? 0,
                    ]);
                }
            }

            if ($userGoalAddOrUpdate) {
                userSubscriptionActivity(); // for subscription activity
                DB::commit();
                // $title = 'Goal Added Successfully';
                // $body = 'Goal name is ' . $request->name;
                // $this->saveNotification([
                //     'user_id' => auth()->user()->id,
                //     'title' => $title,
                //     'description' => $body,
                //     'type' => 'goal',
                //     'for' => 2,
                //     'is_read' => 0
                // ]);
                // $this->sendPushNotificationOnToken(auth()->user()->fcm_token, $title, $body, 'goal');
                $status = true;
                $code = 200;
                $response = [];
                $message = "Goal added successfully";
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "Something went wrong";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userItemList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'nullable|exists:items,uuid',
            'category_id' => 'nullable|exists:categories,uuid',
            'count' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_price' => 'nullable|numeric',
            'end_price' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        try {
            $returnArr = [];
            if (empty($request->uuid)) {
                $query = Item::where('user_id', auth()->user()->id)->latest();
                if (!empty($request->category_id)) {
                    $query->where('category_id', uuidtoid($request->category_id, 'categories'));
                }
                if (!empty($request->count)) {
                    $query->take($request->count);
                }
                if (!empty($request->search)) {
                    $query->where('name', 'like', "%{$request->search}%")
                        ->orWhere('model_no', 'like', "%{$request->search}%")
                        ->orWhere('serial_no', 'like', "%{$request->search}%")
                        ->orWhereHas('category', function ($q) use ($request) {
                            $q->where('title', 'like', "%{$request->search}%");
                        });
                }
                if (!empty($request->start_date) && !empty($request->end_date)) {
                    $query->whereBetween('date', [$request->start_date, $request->end_date]);
                }
                if (!empty($request->start_price) && !empty($request->end_price)) {
                    $query->whereBetween('price', [$request->start_price, $request->end_price]);
                }
                $itemList = $query->paginate($request->per_page ?? 10);
                $returnArr = new PaginateItemCollection($itemList->appends($request->except('page')));
            } else {
                $item = Item::where('user_id', auth()->user()->id)->where('uuid', $request->uuid)->first();
                $returnArr = new ItemCollection($item);
            }
            if ((!empty($itemList) && $itemList->isNotEmpty()) || !empty($item)) {
                $status = true;
                $code = 200;
                $response = $returnArr;
                $message = "Item Fetch Successfully";
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    public function userExpenseList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'nullable|exists:user_expenses,uuid',
            'is_recurring' => 'nullable|in:0,1',
            'month' => 'nullable|numeric',
            'year' => 'nullable|numeric',
            'is_taxable' => 'nullable|in:0,1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        try {
            $month = $request->month ?? Carbon::now()->month;
            $year = $request->year ?? Carbon::now()->year;

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
            } else {
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
            }

            $isTaxable = $request->is_taxable ?? 0;
            $returnArr = [];
            //category wise expense start
            $returnArr['current_month_expense_statistics'] = userExpenseCategoryWise(null, Carbon::now()->month) ?? null;
            $returnArr['last_month_expense_statistics'] = userExpenseCategoryWise(null, Carbon::now()->subMonth()->month) ?? null;
            $returnArr['filter_expense_statistics'] = userExpenseCategoryWiseFilter(null, $startDate, $endDate, $isTaxable, $request->is_recurring ?? 0) ?? null;
            //category wise expense end

            if (empty($request->uuid)) {
                $query = UserExpense::where('user_id', auth()->user()->id)->latest();

                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where(function ($q1) use ($startDate, $endDate) {
                        // For non-recurring expenses: validate with both start_date and end_date
                        $q1->where('is_recurring', 0)
                            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                    })->orWhere(function ($q2) use ($endDate) {
                        // For recurring expenses: validate only with end_date
                        $q2->where('is_recurring', 1)
                            ->where('date', '<=', $endDate->format('Y-m-d'));
                    });
                });

                if (!empty($request->is_recurring) && ($request->is_recurring == 0 || $request->is_recurring == 1)) {
                    $query->where('is_recurring', $request->is_recurring ?? 0);
                }

                if (!empty($request->search)) {
                    $query->where(function ($q) use ($request) {
                        $q->where('name', 'like', "%{$request->search}%")
                            ->orWhere('price', 'like', "%{$request->search}%")
                            ->orWhereHas('category', function ($q2) use ($request) {
                                $q2->where('title', 'like', "%{$request->search}%");
                            });
                    });
                }

                $itemList = $query->with('serviceFrequency', 'expenseRecurring')->get();

                $filteredList = $itemList->map(function ($expense) use ($startDate, $endDate) {
                    if ($expense->is_recurring == 1 && $expense->serviceFrequency) {
                        $frequencyDays = (int) $expense->serviceFrequency->day_count;
                        if ($frequencyDays > 0) {
                            $occurrenceCount = 0;
                            $currentDate = Carbon::parse($expense->date);

                            // Check occurrences until we pass the end date
                            while ($currentDate->lte($endDate)) {
                                if ($currentDate->gte($startDate)) {
                                    $occurrenceCount++;
                                }
                                $currentDate->addDays($frequencyDays);
                            }

                            if ($occurrenceCount > 0) {
                                // Return the expense without multiplying the price
                                return $expense;
                            } else {
                                // Recurring expense but doesn't fall in this range
                                return null;
                            }
                        }
                    }
                    // Non-recurring or recurring without valid frequency in range logic (fallback or standard include if date matches)
                    // If it's not recurring, we only include it if its date is in range.
                    // But our query included 'is_recurring=1' OR 'date in range'.
                    // If it came from 'date in range', keep it.
                    if (
                        Carbon::parse($expense->date)->gte($startDate) &&
                        Carbon::parse($expense->date)->lte($endDate)
                    ) {
                        return $expense;
                    }

                    return null;
                })->filter();

                $returnArr['expense'] = UserExpenseCollection::collection($filteredList);
            } else {
                $item = UserExpense::where('user_id', auth()->user()->id)->where('uuid', $request->uuid)->first();
                $returnArr['expense'] = new UserExpenseCollection($item);
            }
            if ((!empty($filteredList) && $filteredList->isNotEmpty()) || !empty($item)) {
                $status = true;
                $code = 200;
                $response = $returnArr;
                $message = "Expense Fetch Successfully";
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userStatistics(Request $request)
    {
        try {
            $month = $request->month ?? Carbon::now()->month;
            $year = $request->year ?? Carbon::now()->year;
            $returnArr = [];
            $userExpense = UserExpense::where('user_id', auth()->user()->id)->whereMonth('date', $month)->whereYear('date', $year)->sum('price');
            $userItemExpense = Item::where('user_id', auth()->user()->id)->where('is_expense', 1)->whereMonth('date', $month)->whereYear('date', $year)->sum('price');
            $userIncome = UserIncome::where('user_id', auth()->user()->id)->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

            $returnArr = [
                'user_expense' => $userExpense,
                'user_item_expense' => $userItemExpense,
                'user_income' => $userIncome,
                'userExpenseCategoryWise' => userExpenseCategoryWise(null, Carbon::now()->month) ?? null,
                'userLastMonthExpenseCategoryWise' => userExpenseCategoryWise(null, Carbon::now()->subMonth()->month) ?? null,
            ];

            $status = true;
            $code = 200;
            $response = $returnArr;
            $message = "Statistic Found";
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userBudgetList(Request $request)
    {
        try {
            $returnArr = [];
            $userCurrentMonthBudget = userBudgetCategoryWise(auth()->user()->id, $request->is_recurring ?? 0);
            $userPreviousMonthBudget = userBudgetCategoryWise(auth()->user()->id, Carbon::now()->subMonth()->month, $request->is_recurring ?? 0);
            $userUserWiseMonthBudget = userBudgetCategoryWiseRange(auth()->user()->id, $request->month_start ?? null, $request->year_start ?? null, $request->month_end ?? null, $request->year_end ?? null, $request->is_recurring ?? 0);
            // dd($userUserWiseMonthBudget);
            $returnArr['userCurrentMonthBudget'] = $userCurrentMonthBudget;
            $returnArr['userPreviousMonthBudget'] = $userPreviousMonthBudget;
            $returnArr['userUserWiseMonthBudget'] = $userUserWiseMonthBudget;
            if ((!empty($userCurrentMonthBudget) || !empty($userPreviousMonthBudget)) || !empty($userUserWiseMonthBudget)) {
                $status = true;
                $code = 200;
                $response = $returnArr;
                $message = "Budget Fetch Successfully";
            } else {
                $status = false;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userGoalList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'nullable|exists:user_goals,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        try {
            $returnArr = [];
            if (empty($request->uuid)) {
                $userGoal = UserGoal::where('user_id', auth()->user()->id)->latest()->get();
                $returnArr = UserGoalCollection::collection($userGoal);
            } else {
                $userGoalDetail = UserGoal::where('user_id', auth()->user()->id)->where('uuid', $request->uuid)->first();
                $returnArr = new UserGoalCollection($userGoalDetail);
            }
            if (!empty($userGoal) && $userGoal->isNotEmpty() || !empty($userGoalDetail)) {
                $status = true;
                $code = 200;
                $response = $returnArr;
                $message = "Goal Fetch Successfully";
            } else {
                $status = false;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userGoalMark(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'nullable|exists:user_goals,uuid',
            'status' => 'required|in:0,1',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $goal = UserGoal::where('uuid', $request->uuid)->first();
            $goalUpdated = $goal->update(['is_completed' => $request->status]);
            if ($goalUpdated) {
                // $title = 'Goal Completed Successfully';
                // $body = $goal->name . ' marked as completed';
                // $this->saveNotification([
                //     'user_id' => auth()->user()->id,
                //     'title' => $title,
                //     'description' => $body,
                //     'type' => 'goal',
                //     'for' => 2,
                //     'is_read' => 0
                // ]);
                // $this->sendPushNotificationOnToken(auth()->user()->fcm_token, $title, $body, 'goal');
                DB::commit();
                $status = true;
                $code = 200;
                $response = [];
                $message = "Goal Completed Successfully";
            } else {
                $status = false;
                $code = 200;
                $response = [];
                $message = "Something went wrong";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    public function userTaskMark(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'nullable|exists:user_goal_tasks,uuid',
            'status' => 'required|in:0,1',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $goalTask = UserGoalTask::where('uuid', $request->uuid)->first();
            $goalUpdated = $goalTask->update(['is_completed' => $request->status]);
            $userGoal = UserGoal::find($goalTask->user_goal_id);
            if (!empty($userGoal)) {
                if (!$userGoal->tasks()->where('is_completed', 0)->exists()) {
                    $userGoal->update(['is_completed' => 1]);
                }
            }
            if ($goalUpdated) {
                // $title = 'Task Completed Successfully';
                // $body = $goalTask->name . ' marked as completed';
                // $this->saveNotification([
                //     'user_id' => auth()->user()->id,
                //     'title' => $title,
                //     'description' => $body,
                //     'type' => 'task',
                //     'for' => 2,
                //     'is_read' => 0
                // ]);
                // $this->sendPushNotificationOnToken(auth()->user()->fcm_token, $title, $body, 'task');
                DB::commit();
                $status = true;
                $code = 200;
                $response = [];
                $message = "Task Completed Successfully";
            } else {
                $status = false;
                $code = 200;
                $response = [];
                $message = "Something went wrong";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userNotificationList(Request $request)
    {
        try {
            $query = Notification::where(['for' => 2, 'user_id' => auth()->user()->id])
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->latest();
            if (!empty($request->is_read) && ($request->is_read == 0 || $request->is_read == 1)) {
                $query->where('is_read', $request->is_read);
            }
            $notification = $query->get();
            if ($notification) {
                $status = true;
                $code = 200;
                $response = NotificationCollection::collection($notification);
                $message = "Notification fetch successfully";
            } else {
                $status = false;
                $code = 200;
                $response = [];
                $message = "Something went wrong";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userSubscriptionList(Request $request)
    {
        try {
            $subscription = Subscription::where('is_active', 1)->where('price', '>', 0)->get();
            if (!empty($subscription) && $subscription->isNotEmpty()) {
                $status = true;
                $code = 200;
                $response = SubscriptionCollection::collection($subscription);
                $message = "Subscription fetch successfully";
            } else {
                $status = false;
                $code = 200;
                $response = [];
                $message = "No Subscription Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userSubscriptionPurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_id' => 'required|exists:subscriptions,uuid',
            'coupon_id' => 'nullable|exists:coupons,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $subscription = Subscription::where('uuid', $request->subscription_id)->first();
            $totalAmount = null;
            if ($request->coupon_id) {
                $coupon = Coupon::where(['uuid' => $request->coupon_id, 'is_active' => 1])->first();
                if ($coupon) {
                    if ($coupon->type == 1) {
                        //Percentage
                        $discount = ($subscription->price * $coupon->coupon_discount) / 100;
                        $totalAmount = (float) $subscription->price - $discount;
                    } else {
                        //fixed
                        $totalAmount = (float) $subscription->price - $coupon->coupon_discount;
                    }
                }
            }

            // dd($totalAmount);

            $amountTotal = $totalAmount ?? (float) $subscription->price;
            $stripe = new StripeClient(
                env('STRIPE_SECRET_KEY')
            );
            if (auth()->user()->stripe_id != null) {
                $customerId = auth()->user()->stripe_id;
            } else {
                $customer = $stripe->customers->create();
                $customerId = $customer->id;
                auth()->user()->update(['stripe_id' => $customerId]);
            }

            $customer = $stripe->customers->create();

            $ephemeralKey = $stripe->ephemeralKeys->create([
                'customer' => $customerId,
            ], [
                'stripe_version' => '2022-08-01',
            ]);
            $token_info = $stripe->paymentIntents->create([
                'amount' => $amountTotal * 100,
                'currency' => 'AUD',
                'customer' => $customerId,
                'automatic_payment_methods' => [
                    'enabled' => false,
                ],
                'payment_method_types' => [
                    'card'
                ]
            ]);
            if (!empty($token_info->toArray())) {
                $transactionCreated = Transaction::create([
                    'user_id' => auth()->user()->id,
                    'coupon_id' => !empty($coupon) ? $coupon->id : null,
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'mobile_number' => auth()->user()->mobile_number,
                    'subscription_id' => $subscription->id,
                    'amount' => $amountTotal,
                    'transaction_id' => $token_info->id,
                    'payment_mode' => 1,
                    'payment_status' => 1,
                    'order_token' => json_encode($token_info->toArray())
                ]);

                if ($transactionCreated) {
                    DB::commit();
                    $status = true;
                    $code = 200;
                    $message = 'Payment Intent Created Successfully';
                    // $response = ['transaction_uuid' => $transactionCreated->uuid, 'amount' => $amountTotal, 'transaction_id' => $token_info->id, 'order_token' => ($token_info->toArray())];
                    $response = [
                        'transaction_uuid' => $transactionCreated->uuid,
                        'amount' => $amountTotal,
                        'transaction_id' => $token_info->id,
                        'order_token' => ($token_info->toArray()),
                        'ephemeral_key' => $ephemeralKey->secret,
                        'customer_id' => $customerId,
                    ];
                } else {
                    $status = false;
                    $code = 200;
                    $message = 'Something went wrong';
                    $response = [];
                }
            } else {
                $status = false;
                $code = 200;
                $message = 'Something went wrong';
                $response = [];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userSubscriptionResponse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_uuid' => 'required|exists:transactions,uuid',
            'payment_token' => 'required',
            'payment_status' => 'required|in:2,3',
            'coupon_id' => 'nullable|exists:coupons,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $transaction = Transaction::where('uuid', $request->transaction_uuid)->first();
            $transaction->update([
                'payment_token' => json_encode($request->payment_token),
                'payment_status' => $request->payment_status ?? 3,
            ]);

            if ($request->payment_status == 2) {
                $subscription = Subscription::find($transaction->subscription_id);
                $startDate = Carbon::now();
                switch ($subscription->type) {
                    case 1:
                        $endDate = $startDate->copy()->addMonth();
                        break;
                    case 2:
                        $endDate = $startDate->copy()->addMonths(3);
                        break;
                    case 3:
                        $endDate = $startDate->copy()->addMonths(6);
                        break;
                    case 4:
                        $endDate = $startDate->copy()->addYear();
                        break;
                    default:
                        throw new \Exception('Invalid subscription type');
                }
                UserSubscription::where('user_id', auth()->user()->id)->update(['is_active' => 0]);
                $subscriptionCreated = UserSubscription::create([
                    'user_id' => $transaction->user_id,
                    'coupon_id' => !empty($request->coupon_id) ? uuidtoid($request->coupon_id, 'coupons') : null,
                    'transaction_id' => $transaction->id,
                    'subscription_id' => $transaction->subscription_id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'total_activity_count' => $subscription->activity_count,
                    'remaining_activity_count' => $subscription->activity_count,
                    'used_activity_count' => 0,
                ]);

                if ($subscriptionCreated) {
                    DB::commit();
                    $status = true;
                    $code = 200;
                    $message = 'Subscription Created Successfully';
                    $response = ['subscription_uuid' => $subscriptionCreated->uuid];
                } else {
                    $status = false;
                    $code = 500;
                    $message = 'Something went wrong';
                    $response = [];
                }
            } else {
                $status = false;
                $code = 500;
                $message = 'Something went wrong';
                $response = [];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userItemDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $item = Item::where('uuid', $request->item_id)->first();
            $item->delete();
            DB::commit();
            $status = true;
            $code = 200;
            $response = [];
            $message = "Item Deleted Successfully";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userItemImageDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image_id' => 'required|exists:item_images,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $image = ItemImage::where('uuid', $request->image_id)->first();
            $image->delete();
            DB::commit();
            $status = true;
            $code = 200;
            $response = [];
            $message = "Image Deleted Successfully";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    public function userIncomeList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_recurring' => 'nullable|in:0,1',
            'month' => 'nullable|numeric',
            'year' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        try {
            $month = $request->month ?? Carbon::now()->month;
            $year = $request->year ?? Carbon::now()->year;
            $query = UserIncome::where('user_id', auth()->user()->id);
            if ($request->start_date && $request->end_date) {
                if ($request->is_recurring == 1) {
                    $query->where('date', '<=', $request->end_date);
                } else {
                    $query->whereBetween('date', [$request->start_date, $request->end_date]);
                }
            } else {
                $query->whereMonth('date', $month)->whereYear('date', $year);
            }
            if ($request->is_recurring == 0 || $request->is_recurring == 1) {
                $query->where('is_recurring', $request->is_recurring ?? 0);
            }
            if (!empty($request->search)) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            $userIncome = $query->get();
            $totalIncome = $userIncome->sum('amount');
            if (!empty($userIncome)) {
                $status = true;
                $code = 200;
                $response = ['total_income' => $totalIncome, 'log' => UserIncomeCollection::collection($userIncome), 'userIncomeGraph' => getUserIncomeMonthWise()];
                $message = "User Income Successfully";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userNotificationDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        try {
            $notification = Notification::where('uuid', $request->notification_id)->where('user_id', auth()->user()->id)->delete();
            if ($notification) {
                $status = true;
                $code = 200;
                $response = [];
                $message = "Notification Deleted Successfully";
            } else {
                $status = false;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function couponList(Request $request)
    {
        try {
            $userId = auth()->user()->id;

            $couponList = Coupon::where(function ($query) use ($userId) {
                $query->where(['for_all_user' => 1, 'is_active' => 1])
                    ->where(function ($query) {
                        $query->whereNull('start_date')
                            ->orWhere('start_date', '<=', Carbon::today());
                    })
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', Carbon::today());
                    })
                    ->orWhere(function ($query) use ($userId) {
                        $query->where(['for_all_user' => 2, 'is_active' => 1])
                            ->whereIn('id', function ($subQuery) use ($userId) {
                                $subQuery->select('coupon_id')
                                    ->from('user_coupons')
                                    ->where('user_id', $userId);
                            });
                    });
            })->get();
            if ((!empty($couponList) && $couponList->isNotEmpty())) {
                $status = true;
                $code = 200;
                $response = CouponCollection::collection($couponList);
                $message = "Coupon Fetch Successfully";
            } else {
                $status = false;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function bannerList(Request $request)
    {
        try {
            $bannerList = Banner::where(['is_active' => 1])->latest()->get();
            if ((!empty($bannerList) && $bannerList->isNotEmpty())) {
                $status = true;
                $code = 200;
                $response = BannerCollection::collection($bannerList);
                $message = "Banner Fetch Successfully";
            } else {
                $status = false;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userIncomeDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'income_id' => 'required|exists:user_incomes,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $income = UserIncome::where('uuid', $request->income_id)->first();
            $income->delete();
            DB::commit();
            $status = true;
            $code = 200;
            $response = [];
            $message = "Income Deleted Successfully";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    public function userGoalDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goal_id' => 'required|exists:user_goals,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $goal = UserGoal::where('uuid', $request->goal_id)->first();
            $goal->delete();
            DB::commit();
            $status = true;
            $code = 200;
            $response = [];
            $message = "Goal Deleted Successfully";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    public function userTaskDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:user_goal_tasks,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $task = UserGoalTask::where('uuid', $request->task_id)->first();
            $task->delete();
            DB::commit();
            $status = true;
            $code = 200;
            $response = [];
            $message = "Task Deleted Successfully";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    public function userBudgetDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budget_id' => 'required|exists:user_budgets,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $budget = UserBudget::where('uuid', $request->budget_id)->first();
            $budget->delete();
            DB::commit();
            $status = true;
            $code = 200;
            $response = [];
            $message = "Budget Deleted Successfully";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    public function deleteAccount(Request $request)
    {
        DB::beginTransaction();
        try {
            // dd(Auth::user()->id);
            Auth::user()->forceDelete();
            DB::commit();
            $status = true;
            $code = 200;
            $response = [];
            $message = "Account Deleted Successfully";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    public function userExpenseDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_id' => 'required|exists:user_expenses,uuid',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $expense = UserExpense::where('uuid', $request->expense_id)->first();
            $expense->delete();
            DB::commit();
            $status = true;
            $code = 200;
            $response = [];
            $message = "Expense Deleted Successfully";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
}
