<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
  protected $table = 'reviews';
  protected $primaryKey = 'review_id';
  protected $fillable = ['user_id','product_id','user_product_id','rating','comment'];

  public function user(){ return $this->belongsTo(User::class, 'user_id','user_id'); }
  public function product(){ return $this->belongsTo(Product::class, 'product_id','products_id'); }
  public function userProduct(){ return $this->belongsTo(UserProduct::class, 'user_product_id','user_product_id'); }
}
