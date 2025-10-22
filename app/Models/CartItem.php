<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CartItem
 * 
 * @property int $id_cart_items
 * @property int $cart_id
 * @property int $products_id
 * @property int $count
 * @property float $unit_price
 * 
 * @property Cart $cart
 * @property Product $product
 *
 * @package App\Models
 */
class CartItem extends Model
{
	protected $table = 'cart_items';
	protected $primaryKey = 'id_cart_items';
	public $timestamps = false;

	protected $casts = [
		'cart_id' => 'int',
		'products_id' => 'int',
		'count' => 'int',
		'unit_price' => 'float'
	];

	protected $fillable = [
		'cart_id',
		'products_id',
		'count',
		'unit_price'
	];

	protected $guarded = [];

	public function cart()
	{
		return $this->belongsTo(Cart::class, 'cart_id');
	}

	public function product()
	{
		return $this->belongsTo(Product::class, 'products_id');
	}
}
