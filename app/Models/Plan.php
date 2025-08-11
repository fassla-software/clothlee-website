<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
  	
   protected $fillable = [
        'name',
        'description',
        'commission',
     	'logo',
     	'status',
     	'interval'
    ];

    protected $casts = [
        'commission' => 'decimal:2',
    ];

  
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
  
  
  
}
