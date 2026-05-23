<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class AnalyticsController extends BaseController
{
    public function index()
    {
        return view('artist.analytics.index');
    }
}
