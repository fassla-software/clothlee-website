<?php

namespace App\Http\Controllers\Api\V2\Attribute;

use App\Http\Controllers\Api\V2\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;


class AttributeController extends Controller
{
    public function index($slug)
    {
        $attr = Attribute::whereRaw('LOWER(name) = ?', [strtolower($slug)])->with('attribute_values')->first();
      	if(!$attr)
        {
          return response()->json('Not Found', 404);
        }
        return response()->json($attr);
    }

}
