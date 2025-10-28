<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProduct extends Model
{
  protected $table = 'user_products';
  protected $primaryKey = 'user_product_id';
  protected $fillable = [
    'user_id','product_type_id','name','description','price','stock',
    'img_name','condition','report_count'
  ];

  public function owner(){ return $this->belongsTo(User::class, 'user_id', 'user_id'); }
  public function type(){ return $this->belongsTo(ProductType::class, 'product_type_id', 'product_type_id'); }
  public function reviews(){ return $this->hasMany(Review::class, 'user_product_id', 'user_product_id'); }
  public function reports(){ return $this->hasMany(Report::class, 'user_product_id', 'user_product_id'); }
}
