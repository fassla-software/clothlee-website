<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use App\Traits\PreventDemoModeChanges;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use Storage;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, ToModel
{
    use PreventDemoModeChanges;

    private $rows = 0;

    public function collection(Collection $rows)
    {
        $canImport = true;
        $user = Auth::user();
        if ($user->user_type == 'seller' && addon_is_activated('seller_subscription')) {
            if ((count($rows) + $user->products()->count()) > $user->shop->product_upload_limit
                || $user->shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($user->shop->package_invalid_at), false) < 0
            ) {
                $canImport = false;
                flash(translate('Please upgrade your package.'))->warning();
            }
        }

        if ($canImport) {
            foreach ($rows as $row) {
                $approved = 1;
                if ($user->user_type == 'seller' && get_setting('product_approve_by_admin') == 1) {
                    $approved = 0;
                }

                // Generate a unique name for the product
                $generatedName = 'Product-' . Str::random(10); // Auto-generated name

                // Create the product with all fields nullable
                $productId = Product::create([
                    'name' => $generatedName, // Use the generated name
                    'slug' => Str::slug($generatedName), // Generate slug from the name
                  'meta_img' => isset($row['thumbnail_img']) ? $this->downloadThumbnail($row['thumbnail_img']) : null, // Use the same thumbnail_img for meta_img
               		'meta_title'=> $generatedName, // Use the generated name
                    'added_by' => $user->user_type == 'seller' ? 'seller' : 'admin',
                    'user_id' => $user->user_type == 'seller' ? $user->id : User::where('user_type', 'admin')->first()->id,
                    'approved' => $approved,
                    'category_id' => isset($row['category_id']) ? $row['category_id'] : null,
                    'video_provider' => isset($row['video_provider']) ? $row['video_provider'] : null,
                    'video_link' => isset($row['video_link']) ? $row['video_link'] : null,
                    'unit_price' => isset($row['unit_price']) ? $row['unit_price'] : null,
                    'est_shipping_days' => isset($row['est_shipping_days']) ? $row['est_shipping_days'] : null,
                    'fabric' => isset($row['fabric']) ? $row['fabric'] : null,
                    'min_qty' => isset($row['min_qty']) ? $row['min_qty'] : null,
					'colors' => json_encode([]),
                    'choice_options' => json_encode([]), // Can be empty or modified as per your logic
                    'variations' => json_encode([]), // Can be empty or modified as per your logic
                    'thumbnail_img' => isset($row['thumbnail_img']) ? $this->downloadThumbnail($row['thumbnail_img']) : null,
                    'photos' => isset($row['photos']) ? $this->downloadGalleryImages($row['photos']) : null,
                ]);

              
              // Handle VAT & Tax - Create product_tax entries
if (isset($row['tax_id']) && isset($row['tax']) && isset($row['tax_type'])) {
    \App\Models\ProductTax::create([
        'product_id' => $productId->id,
        'tax_id' => $row['tax_id'],
        'tax' => $row['tax'],
        'tax_type' => $row['tax_type'],
    ]);
}
                // Create product stock
                ProductStock::create([
                    'product_id' => $productId->id,
                    'qty' => isset($row['current_stock']) ? $row['current_stock'] : 0,
                    'price' => isset($row['unit_price']) ? $row['unit_price'] : 0,
                    'variant' => '', // Add logic for variant if needed
                ]);

                // Handle multi-category association if present
                if (isset($row['multi_categories']) && $row['multi_categories'] != null) {
                    foreach (explode(',', $row['multi_categories']) as $category_id) {
                        ProductCategory::insert([
                            "product_id" => $productId->id,
                            "category_id" => $category_id
                        ]);
                    }
                }
            }

            flash(translate('Products imported successfully'))->success();
        }
    }

    public function model(array $row)
    {
        ++$this->rows;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function rules(): array
    {
        return [
            // Can also use callback validation rules
            'unit_price' => function ($attribute, $value, $onFailure) {
                if (!is_numeric($value)) {
                    $onFailure('Unit price is not numeric');
                }
            }
        ];
    }

    public function downloadThumbnail($url)
    {
        try {
            $upload = new Upload;
            $upload->external_link = $url;
            $upload->type = 'image';
            $upload->save();

            return $upload->id;
        } catch (\Exception $e) {
        }
        return null;
    }

    public function downloadGalleryImages($urls)
    {
        $data = array();
        foreach (explode(',', str_replace(' ', '', $urls)) as $url) {
            $data[] = $this->downloadThumbnail($url);
        }
        return implode(',', $data);
    }
}
