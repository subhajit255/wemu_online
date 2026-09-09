<?php

namespace App\Http\Controllers\Api;

use App\Models\Cms;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class CmsController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/pages",
     *     summary="Get CMS pages",
     *     tags={"CMS"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="query",
     *         description="Slug of the page (e.g., terms-conditions, privacy-policy, contact-us, community-guidelines)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Pages fetched successfully")
     * )
     */
    public function cmsPages(Request $request)
    {
        try {
            $query = Cms::where("is_active", 1);
            if ($request->has('slug')) {
                $slug = $request->slug;

                // Map the frontend slugs to database aliases
                $aliasMap = [
                    'terms-condition'      => 'wemu_terms_of_use',
                    'terms-conditions'     => 'wemu_terms_of_use',
                    'terms-and-condition'  => 'wemu_terms_of_use',
                    'terms-and-conditions' => 'wemu_terms_of_use',
                    'privacy-policy'       => 'privacy_policy',
                    'contact-us'           => 'contact_us',
                    'community-guidelines' => 'community_guidelines',
                ];
                $dbAlias = $aliasMap[$slug] ?? str_replace('-', '_', $slug);
                
                // Fallback to exactly matching the alias if no map matched
                $query->where(function ($q) use ($dbAlias, $slug) {
                    $q->where('alias', $dbAlias)->orWhere('alias', $slug);
                });

                // When a specific slug is requested, return the single page object
                $cmsPage = $query->first();
                return $this->responseJson(true, 200, "Page fetch successfully", $cmsPage);
            }
            $cmsPages = $query->get();
            return $this->responseJson(true, 200, "Pages fetch successfully", $cmsPages);
        } catch (\Exception $ex) {
            return $this->responseJson(false, 500, "Something went wrong");
        }
    }
}
