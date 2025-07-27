<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterCategory;
use Illuminate\Http\Request;

class MasterCategoryController extends Controller
{
    public function index()
    {
        $categories = MasterCategory::select('id', 'name', 'name_ar', 'description', 'image')->get();

        return response()->json([
            'status' => true,
            'message' => 'Master categories list',
            'data' => $categories,
        ]);
    }
}
