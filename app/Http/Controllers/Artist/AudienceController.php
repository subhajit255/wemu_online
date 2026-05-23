<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class AudienceController extends BaseController
{
    public function index()
    {
        return view('artist.audience.index');
    }
}
