<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductType
 * 
 * @property int $product_type_id
 * @property string $name
 * @property string $description
 * 
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class ProductType extends Model
{
	protected $table = 'product_types';
	protected $primaryKey = 'product_type_id';
	public $timestamps = false;

	protected $fillable = [
		'name',
		'description'
	];

	public function products()
	{
		return $this->hasMany(Product::class, 'product_type_id', 'product_type_id');
	}
}
