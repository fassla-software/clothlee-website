<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
  
  
  
    protected $fillable = [
        'plan_id',
        'shop_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
  
  
   public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the user that owns the subscription
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

}
