<?php

namespace App\Http\Controllers\Api\V2;


use App\Models\Search;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Shop;
use Illuminate\Http\Request;

class SearchSuggestionController extends Controller
{
    /*public function getList(Request $request)
    {
        $query_key = $request->query_key;
        $type = $request->type;

        $search_query  = Search::select('id', 'query', 'count');
        if ($query_key != "") {
            $search_query->where('query', 'like', "%{$query_key}%");
        }
        $searches = $search_query->orderBy('count', 'desc')->limit(10)->get();

        if ($type == "product") {
            $product_query = Product::query();
            if ($query_key != "") {
                $product_query->where(function ($query) use ($query_key) {
                    foreach (explode(' ', trim($query_key)) as $word) {
                        $query->where('name', 'like', '%'.$word.'%')->orWhere('tags', 'like', '%'.$word.'%')->orWhereHas('product_translations', function($query) use ($word){
                            $query->where('name', 'like', '%'.$word.'%');
                        });
                    }
                });
            }

            $products = filter_products($product_query)->limit(3)->get();
        }

        if ($type == "brands") {
            $brand_query = Brand::query();
            if ($query_key != "") {
                $brand_query->where('name', 'like', "%$query_key%");
            }

            $brands = $brand_query->limit(3)->get();
        }

        if ($type == "sellers") {
            $shop_query = Shop::query();
            if ($query_key != "") {
                $shop_query->where('name', 'like', "%$query_key%");
            }

            $shops = $shop_query->limit(3)->get();
        }



        $items = [];

        //shop push
        if ($type == "sellers" &&  !empty($shops)) {
            foreach ($shops as  $shop) {
                $item = [];
                $item['id'] = $shop->id;
                $item['query'] = $shop->name;
                $item['count'] = 0;
                $item['type'] = "shop";
                $item['type_string'] = "Shop";

                $items[] = $item;
            }
        }

        //brand push
        if ($type == "brands" && !empty($brands)) {
            foreach ($brands as  $brand) {
                $item = [];
                $item['id'] = $brand->id;
                $item['query'] = $brand->name;
                $item['count'] = 0;
                $item['type'] = "brand";
                $item['type_string'] = "Brand";

                $items[] = $item;
            }
        }
    
        //product push
        if ($type == "product" &&  !empty($products)) {
            foreach ($products as  $product) {
                $item = [];
                $item['id'] = $product->id;
                $item['query'] = $product->name;
                $item['count'] = 0;
                $item['type'] = "product";
                $item['type_string'] = "Product";

                $items[] = $item;
            }
        }

        //search push
        if (!empty($searches)) {
            foreach ($searches as  $search) {
                $item = [];
                $item['id'] = $search->id;
                $item['query'] = $search->query;
                $item['count'] = intval($search->count);
                $item['type'] = "search";
                $item['type_string'] = "Search";

                $items[] = $item;
            }
        }

        return $items; // should return a valid json of search list;
    }*/
  
  public function getList(Request $request)
{
    $query_key = $request->query_key;
    $type = $request->type;

    $search_query = Search::select('id', 'query', 'count');
    if (!empty($query_key)) {
        $search_query->where('query', 'like', "%{$query_key}%");
    }
    $searches = $search_query->orderBy('count', 'desc')->limit(10)->get();

    if ($type == "product") {
        $product_query = Product::query()->with('main_category'); // Load category relationship

        if (!empty($query_key)) {
            $product_query->where(function ($query) use ($query_key) {
                foreach (explode(' ', trim($query_key)) as $word) {
                    $query->orWhereHas('main_category', function ($query) use ($word) {
                        $query->where('name', 'like', '%' . $word . '%');
                    });
                }
            });
        }

        $products = filter_products($product_query)->limit(3)->get();
    }

    if ($type == "brands") {
        $brand_query = Brand::query();
        if (!empty($query_key)) {
            $brand_query->where('name', 'like', "%$query_key%");
        }
        $brands = $brand_query->limit(3)->get();
    }

    if ($type == "sellers") {
        $shop_query = Shop::query();
        if (!empty($query_key)) {
            $shop_query->where('name', 'like', "%$query_key%");
        }
        $shops = $shop_query->limit(3)->get();
    }

    $items = [];

    // Shop push
    if ($type == "sellers" && !empty($shops)) {
        foreach ($shops as $shop) {
            $items[] = [
                'id' => $shop->id,
                'query' => $shop->name,
                'count' => 0,
                'type' => "shop",
                'type_string' => "Shop"
            ];
        }
    }

    // Brand push
    if ($type == "brands" && !empty($brands)) {
        foreach ($brands as $brand) {
            $items[] = [
                'id' => $brand->id,
                'query' => $brand->name,
                'count' => 0,
                'type' => "brand",
                'type_string' => "Brand"
            ];
        }
    }

    // Product push - Use the category (subcategory) name instead of the product name
    if ($type == "product" && !empty($products)) {
        foreach ($products as $product) {
            // Get subcategory name
            $subcategoryName = optional($product->main_category)->getTranslation('name');

            $items[] = [
                'id' => $product->id,
                'query' => $subcategoryName ?? "Unknown Category", // Always return subcategory name
                'count' => 0,
                'type' => "product",
                'type_string' => "Product"
            ];
        }
    }

    // Search push
    if (!empty($searches)) {
        foreach ($searches as $search) {
            $items[] = [
                'id' => $search->id,
                'query' => $search->query,
                'count' => intval($search->count),
                'type' => "search",
                'type_string' => "Search"
            ];
        }
    }

    return response()->json($items);
}

}
