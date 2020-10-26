<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;

class ProductController extends Controller
{
	public function getProducts(Request $request){  
		$response = [];
		$response['success'] = FALSE;
		$product = Product::get();
		if($product){

			$response['success'] = TRUE;
			$response['message']='Data found';
			$response['data']=$product;
			return $response;
			
		}
		$response['message'] = "No product found";
		return $response;
	}
}
