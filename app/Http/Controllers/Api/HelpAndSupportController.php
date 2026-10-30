<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\SupportArticle;
use App\Models\UserQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HelpAndSupportController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/support-articles",
     *     summary="Get support articles",
     *     tags={"Help & Support"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter articles by category name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword for question or answer",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Articles fetched successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function supportArticles(Request $request)
    {
        try {
            $query = SupportArticle::active();

            if ($request->filled('category')) {
                $query->where('category', trim($request->category));
            }

            $search = $request->input('search') ?? $request->input('keyword');
            if (!empty($search)) {
                $searchTerm = trim($search);
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('question', 'like', '%' . $searchTerm . '%')
                        ->orWhere('answer', 'like', '%' . $searchTerm . '%');
                });
            }

            $articles = $query->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Fallback: If DB table is empty and no specific filters were applied, run seeder
            if ($articles->isEmpty() && !$request->filled('category') && empty($search)) {
                if (SupportArticle::count() === 0 && class_exists(\Database\Seeders\SupportArticleSeeder::class)) {
                    try {
                        (new \Database\Seeders\SupportArticleSeeder())->run();
                        $articles = SupportArticle::active()
                            ->orderBy('sort_order', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();
                    } catch (\Throwable $seederException) {
                        logger()->warning('SupportArticleSeeder auto-run error: ' . $seederException->getMessage());
                    }
                }
            }

            // Group articles by category preserving standard display order
            $defaultCategoryOrder = ['Payment & billing', 'Manage your account', 'Security & privacy'];
            $grouped = $articles->groupBy('category')->sortBy(function ($items, $category) use ($defaultCategoryOrder) {
                $index = array_search($category, $defaultCategoryOrder, true);
                return $index === false ? 999 : $index;
            });

            $response = [];
            foreach ($grouped as $category => $items) {
                $response[] = [
                    'category' => $category,
                    'articles' => $items->map(function ($article) {
                        return [
                            'id' => $article->id,
                            'uuid' => $article->uuid,
                            'category' => $article->category,
                            'question' => $article->question,
                            'answer' => $article->answer,
                            'sort_order' => $article->sort_order,
                        ];
                    })->values()->all(),
                ];
            }

            return $this->responseJson(true, 200, "Articles fetched successfully", $response);
        } catch (\Throwable $e) {
            logger()->error('Support articles API error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
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
