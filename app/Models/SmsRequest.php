<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;

class SmsRequest extends Model
{
    use HasFactory;
  
  	protected $fillable=[
    	
      'shop_id',
      'phone',
      'message',
      'status'
    ];
  
  
  public function shop()
  {
	return $this->belongsto(Shop::class);
  }
  
  
  
}
