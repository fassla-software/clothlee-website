<?php

namespace App\Http\Controllers\Api\V2\Store;

use App\Http\Controllers\Api\V2\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Color;
use App\Http\Resources\V2\ProductCollection;
use App\Http\Resources\V2\ProductMiniCollection;

class StoreCategoryController extends Controller
{
  
public function storeCategory($slug)
{
    $shop = Shop::where('slug', $slug)->firstOrFail();
    $userId = $shop->user_id;

    // Get unique category IDs from the products of the current store
    $categoryIds = Product::where('user_id', $userId)
        ->pluck('category_id')
        ->unique();

    // Get parent category IDs (main categories)
    $parentIds = Category::whereIn('id', $categoryIds)
        ->pluck('parent_id')
        ->unique()
        ->filter();

    // Get main categories with their products
    $categories = Category::whereIn('id', $parentIds)
        ->select('id', 'name', 'icon', 'slug')
        ->get()
        ->map(function ($category) use ($userId, $shop) {
            // Get products for each main category
            $products = Product::where('user_id', $userId)
                ->whereIn('category_id', $category->categories->pluck('id'))
                ->where('published', 1);

            // Apply default sort
            switch ($shop->default_sort) {
                case 'cheapest':
                    $products = $products->orderBy('unit_price', 'asc');
                    break;
                case 'newest':
                default:
                    $products = $products->orderBy('created_at', 'desc');
                    break;
            }

            $products = $products->get();

            return [
                'name' => $category->name,
                'icon' => uploaded_asset($category->icon),
                'slug' => $category->slug,
                'products' => new ProductMiniCollection($products),
            ];
        });

    return response()->json([
        'message' => 'Store Main Categories with Products',
        'default_sort' => $shop->default_sort,
        'categories' => $categories,
    ]);
}

  
  
	public function storeSubCategory($store_slug, $category_slug)
    {
        $shop = Shop::where('slug', $store_slug)->firstOrFail();

        $userId = $shop->user_id;

        $mainCategory = Category::where('slug', $category_slug)->firstOrFail();
		
        $categoryIds = Product::where('user_id', $userId)->pluck('category_id')->unique();

        $subcategories = Category::whereIn('id', $categoryIds)
            ->select('name', 'icon', 'slug')
            ->get()
            ->map(function ($subcategory) {
                return [
                    'name' => $subcategory->name,
                    'icon' => uploaded_asset($subcategory->icon),
                    'slug' => $subcategory->slug,
                ];
            });

        return response()->json([
            'message' => 'Store Subcategories',
            'subcategories' => $subcategories,
        ]);
    }
  
  
  
  
public function SubCategoryProduct(Request $request, $store_slug, $category_slug, $sub_category_slug)
{
    try {
        // Step 1: Get the shop (store) based on the slug
        $shop = Shop::where('slug', $store_slug)->firstOrFail();
        $userId = $shop->user_id;  // Get the user ID for the store

        // Step 2: Get the category using the category_slug
        $mainCategory = Category::where('slug', $category_slug)->firstOrFail();

        // Step 3: Get the subcategory using the sub_category_slug
        $subcategory = Category::where('slug', $sub_category_slug)
            ->where('parent_id', $mainCategory->id)
            ->firstOrFail();

        // Step 4: Get the products that belong to the subcategory and are linked to the specific store's user
        $products = Product::where('user_id', $userId)
            ->where('category_id', $subcategory->id);

        // Filter by Price
        if ($request->has('min_price') || $request->has('max_price')) {
            $products = $products->get()->filter(function ($product) use ($request) {
                $discounted_price = home_discounted_base_price($product, false);
                $min_price = $request->min_price ? (float) $request->min_price : 0;
                $max_price = $request->max_price ? (float) $request->max_price : PHP_INT_MAX;
                return $discounted_price >= $min_price && $discounted_price <= $max_price;
            })->values();
        } else {
            $products = $products->get();
        }

        // Filter by Size
        if ($request->has('size')) {
            $sizeFilter = strtolower($request->size); // Convert input to lowercase
            $products = $products->filter(function ($product) use ($sizeFilter) {
                $choice_options = json_decode($product->choice_options, true);
                if (!is_array($choice_options)) return false;

                foreach ($choice_options as $option) {
                    $attribute = Attribute::find($option['attribute_id']);
                    if ($attribute && strtolower($attribute->name) === 'size') {
                        // Convert all values to lowercase before checking
                        $lowercaseValues = array_map('strtolower', $option['values']);
                        return in_array($sizeFilter, $lowercaseValues);
                    }
                }
                return false;
            })->values();
        }
		
      

        // Filter by Color
        if ($request->has('color') && $request->color != null) {
            $colorCode = '#' . $request->color; // Ensure the color code is prefixed with #

            $products = $products->filter(function ($product) use ($colorCode) {
                $productColors = json_decode($product->colors, true); // Decode JSON

                // Ensure it's an array and check if the color exists
                return is_array($productColors) && in_array($colorCode, $productColors);
            })->values(); // Reset keys
        }




        // Step 5: Map the products to the desired format
        $formattedProducts = $products->map(function ($data) {
            $wholesale_product = ($data->wholesale_product == 1) ? true : false;
            return [
                'id' => $data->id,
                'slug' => $data->slug,
                'name' => $data->getTranslation('name'),
                'thumbnail_image' => uploaded_asset($data->thumbnail_img),
                'has_discount' => home_base_price($data, false) != home_discounted_base_price($data, false),
                'discount' => "-" . discount_in_percentage($data) . "%",
                'stroked_price' => home_base_price($data),
                'main_price' => home_discounted_base_price($data),
                'rating' => (float) $data->rating,
                'sales' => (int) $data->num_of_sale,
                'is_wholesale' => $wholesale_product,
              	'size' => collect(json_decode($data->choice_options, true))
                    ->filter(function ($option) {
                        $attribute = Attribute::find($option['attribute_id']);
                        return $attribute && strtolower($attribute->name) === 'size';
                    })
                    ->pluck('values')
                    ->flatten()
                    ->values(),
                'links' => [
                    'details' => route('products.show', $data->id),
                ]
            ];
        });

        // Step 6: Return the formatted products in a structured JSON response
        return response()->json($formattedProducts);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Category or Subcategory not found.',
            'error' => $e->getMessage()
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred.',
            'error' => $e->getMessage()
        ], 500);
    }
}




}
