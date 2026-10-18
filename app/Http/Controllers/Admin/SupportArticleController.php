<?php

namespace App\Http\Controllers\Admin;

use App\Models\SupportArticle;
use App\Traits\CommonFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class SupportArticleController extends BaseController
{
    use CommonFunction;

    /**
     * Display a listing of the support articles grouped by category for accordion cards.
     */
    public function index(Request $request)
    {
        $query = SupportArticle::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', '%' . $search . '%')
                  ->orWhere('answer', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && in_array($request->status, ['0', '1', 0, 1])) {
            $query->where('is_active', (int) $request->status);
        }

        $articles = $query->orderBy('category', 'asc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Group articles by category for the accordion display
        $articlesByCategory = $articles->groupBy('category');

        // Master categories list for dropdowns & filters
        $defaultCategories = ['Payment & billing', 'Manage your account', 'Security & privacy'];
        $dbCategories = SupportArticle::select('category')->distinct()->pluck('category')->toArray();
        $allCategories = array_values(array_unique(array_filter(array_merge($defaultCategories, $dbCategories))));

        // Summary counts
        $totalCount = SupportArticle::count();
        $activeCount = SupportArticle::where('is_active', 1)->count();
        $inactiveCount = SupportArticle::where('is_active', 0)->count();

        return view('admin.support_article.index', compact(
            'articles',
            'articlesByCategory',
            'allCategories',
            'totalCount',
            'activeCount',
            'inactiveCount'
        ));
    }

    /**
     * Store or update a support article.
     */
    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $id = $request->id ?? null;
            $isUpdate = !empty($id);

            $rules = [
                'category' => 'required|string|max:150',
                'question' => 'required|string|max:500',
                'answer' => 'required|string',
                'sort_order' => 'nullable|integer',
            ];

            $request->validate($rules);

            DB::beginTransaction();
            try {
                $postData = [
                    'category' => trim($request->category),
                    'question' => trim($request->question),
                    'answer' => $request->answer,
                    'sort_order' => $request->sort_order ?? 0,
                ];

                if ($request->has('is_active')) {
                    $postData['is_active'] = $request->is_active ? 1 : 0;
                } elseif (!$isUpdate) {
                    $postData['is_active'] = 1;
                }

                $article = SupportArticle::updateOrCreate(['id' => $id], $postData);
                DB::commit();

                $message = $isUpdate
                    ? 'Support Article updated successfully.'
                    : 'Support Article created successfully.';

                return $this->responseJson(true, 200, $message, $article);
            } catch (\Throwable $th) {
                DB::rollBack();
                $response = [
                    'Message' => $th->getMessage(),
                    'File Path' => $th->getFile(),
                    'Line Number' => $th->getLine()
                ];
                $message = config('constants.CATCH_ERROR_MSG') ?? 'An error occurred while saving the article.';
                return $this->responseJson(false, 500, $message, $response);
            }
        }

        return redirect()->route('admin.support-article.list');
    }

    /**
     * Get single support article details for AJAX editing.
     */
    public function edit(Request $request, $uuid)
    {
        $article = SupportArticle::where('uuid', $uuid)
            ->orWhere('id', $uuid)
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'data' => $article
        ]);
    }

    /**
     * Delete a support article.
     */
    public function delete(Request $request, $uuid = null)
    {
        $targetUuid = $uuid ?? $request->uuid;
        $article = SupportArticle::where('uuid', $targetUuid)
            ->orWhere('id', $targetUuid)
            ->firstOrFail();

        $article->delete();

        return $this->responseJson(true, 200, 'Support Article deleted successfully.');
    }
}
