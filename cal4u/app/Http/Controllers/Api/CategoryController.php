<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Log;

class CategoryController extends Controller
{
    public function getAllCategories(Request $request)
    {
        $response = [];
        try {
            $response['data'] = Category::getAllCategories($request);
            $response['success'] = API_SUCCESS;
            $response['status'] = API_STATUS_OK;
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            $response = apiResponseServerError($e);
        }
        return $response;
    }
}
