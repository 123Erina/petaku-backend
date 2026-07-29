<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\Place;
use App\Models\Category;
use App\Models\User;
use App\Models\Report;
use App\Models\FiberOptik;
use App\Models\Basemap;
use App\Models\GoogleBusiness;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'opd'            => Opd::count(),
            'place'          => Place::count(),
            'user'           => User::count(),
            'category'       => Category::count(),
            'report'         => Report::count(),
            'fiber'          => FiberOptik::count(),
            'basemap'        => Basemap::count(),
            'googleBusiness' => GoogleBusiness::count(),
        ]);
    }
}
