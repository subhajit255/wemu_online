<?php

namespace App\Http\Controllers\Api;

use App\Models\Cms;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class CmsController extends BaseController
{
    public function cmsPages(Request $request)
    {
        try {
            $cmsPages = Cms::where("is_active", 1)->get();
            $query = Cms::where("is_active", 1);
            if ($request->has('type')) {
                $type = $request->type;

                // Map the frontend types to database aliases
                $aliasMap = [
                    'terms-and-condition' => 'wemu_terms_of_use',
                    'privacy-policy'      => 'wemu_privacy_policy',
                ];
                $dbAlias = $aliasMap[$type] ?? 'wemu_' . str_replace('-', '_', $type);
                $query->where('alias', $dbAlias);

                // When a specific type is requested, return the single page object
                $cmsPage = $query->first();
                return $this->responseJson(true, 200, "Page fetch successfully", $cmsPage);
            }
            $cmsPages = $query->get();
            return $this->responseJson(true, 200, "Page fetch successfully", $cmsPages);
        } catch (\Exception $ex) {
            return $this->responseJson(false, 500, "Something went wrong");
        }
    }
}
