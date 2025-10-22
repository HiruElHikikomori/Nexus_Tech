<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property int $products_id
 * @property string $name
 * @property int $product_type_id
 * @property string $description
 * @property float $price
 * @property int $stock
 * @property string $img_name
 * 
 * @property ProductType $product_type
 * @property Collection|CartItem[] $cart_items
 *
 * @package App\Models
 */
class Product extends Model
{
	protected $table = 'products';
	protected $primaryKey = 'products_id';
	public $timestamps = false;

	protected $casts = [
		'product_type_id' => 'int',
		'price' => 'float',
		'stock' => 'int'
	];

	protected $fillable = [
		'name',
		'product_type_id',
		'description',
		'price',
		'stock',
		'img_name'
	];

	protected $guarded = []; // Esto permite que los campos en $fillable sean asignados masivamente.

	public function product_type()
	{
		return $this->belongsTo(ProductType::class, 'product_type_id', 'product_type_id');
	}

	public function cart_items()
	{
		return $this->hasMany(CartItem::class, 'products_id');
	}
}
