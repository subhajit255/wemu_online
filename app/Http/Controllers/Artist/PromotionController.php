<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class PromotionController extends BaseController
{
    public function index()
    {
        return view('artist.promotion.index');
    }
}
