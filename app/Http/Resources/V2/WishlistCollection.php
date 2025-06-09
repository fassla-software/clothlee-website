<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WishlistCollection extends ResourceCollection
{
    /*public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'id' => (integer) $data->id,
                    'product' => [
                        'id' => $data->product->id,
                        'name' => $data->product->name,
                        'slug' => $data->product->slug,
                        'thumbnail_image' => uploaded_asset($data->product->thumbnail_img),
                        'base_price' => format_price(home_base_price($data->product, false)) ,
                        'rating' => (double) $data->product->rating,
                    ]
                ];
            })
        ];
    }*/
  
      public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $wholesale_product =
                    ($data->product->wholesale_product == 1) ? true : false;
                return [
                    'id' => $data->product->id,
                    'slug' => $data->product->slug,
                    'name' => $data->product->getTranslation('name'),
                	'category_name' => optional($data->product->categories->first())->getTranslation('name'),
					'subcategory_name' => optional($data->product->categories->first()?->childrenCategories->first())->getTranslation('name'),

       				'fabric' => $data->product->fabric,
                    'slug' => $data->product->slug,
                    'thumbnail_image' => uploaded_asset($data->product->thumbnail_img),
                    'has_discount' => home_base_price($data->product, false) != home_discounted_base_price($data->product, false),
                    'discount' => "-" . discount_in_percentage($data->product) . "%",
                    'stroked_price' => home_base_price($data->product),
                    'main_price' => home_discounted_base_price($data->product),
                    'rating' => (float) $data->product->rating,
                    'sales' => (int) $data->product->num_of_sale,
                    'is_wholesale' => $wholesale_product,
                    'size' => collect(json_decode($data->product->choice_options, true))
                      ->filter(function ($option) {
                          $attribute = Attribute::find($option['attribute_id']);
                          return $attribute && strtolower($attribute->name) === 'size';
                      })
                      ->pluck('values')
                      ->flatten()
                      ->values(),
                    'links' => [
                        'details' => route('products.show', $data->product->id),
                    ]
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
