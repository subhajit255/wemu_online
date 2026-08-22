<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Models\UserQuery;
use Illuminate\Support\Facades\Validator;

class HelpAndSupportController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/support-articles",
     *     summary="Get support articles",
     *     tags={"Help & Support"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Articles fetched successfully")
     * )
     */
    public function supportArticles(Request $request)
    {
        try {
            $allCategories = config("constants.SUPPORT_ARTICLE");
            $response = [];
            foreach ($allCategories as $key => $value) {
                $response[] = [
                    "category" => $key,
                    "articles" => $value
                ];
            }
            return $this->responseJson(true, 200, "Success", $response);
        } catch (\Exception $e) {
            return $this->responseJson(false, 500, "something went wrong");
        }
    }
    /**
     * @OA\Post(
     *     path="/api/raise-help",
     *     summary="Raise a help request",
     *     tags={"Help & Support"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="subject", type="string", description="Max 255 chars"),
     *                 @OA\Property(property="queries", type="string", description="Help query text"),
     *                 required={"subject", "queries"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Help requested successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function raiseHelp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'queries' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }

        DB::beginTransaction();
        try {
            $helpAndSupport = UserQuery::create([
                'user_id' => auth()->user()->id,
                'subject' => $request->subject ?? null,
                'query' => $request->queries ?? null
            ]);
            DB::commit();
            return $this->responseJson(true, 200, "Query raised successfully", []);
        } catch (\Exception $ex) {
            DB::rollBack();
            logger($ex->getMessage() . ' -- ' . $ex->getLine() . ' -- ' . $ex->getFile());
            return $this->responseJson(false, 500, "something went wrong", []);
        }
    }
}
