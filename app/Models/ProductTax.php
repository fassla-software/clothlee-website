<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class ProductTax extends Model
{
    use PreventDemoModeChanges;

    // Add the product_id to the fillable property to allow mass assignment
    protected $fillable = [
        'product_id',
        'tax_id',
        'tax',
        'tax_type',
    ];
}
