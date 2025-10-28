<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
  protected $table = 'order_items';
  protected $primaryKey = 'order_item_id';
  protected $fillable = ['order_id','product_id','user_product_id','count','unit_price'];

  public function order(){ return $this->belongsTo(Order::class, 'order_id','order_id'); }
  public function product(){ return $this->belongsTo(Product::class, 'product_id','products_id'); }
  public function userProduct(){ return $this->belongsTo(UserProduct::class, 'user_product_id','user_product_id'); }
}
