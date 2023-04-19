<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{

    public static function createImageName($product_id): string
    {
        return $product_id . '-' . time() . '-' . rand(0,100);
    }

}
