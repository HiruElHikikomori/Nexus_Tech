<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cart
 * 
 * @property int $cart_id
 * @property int $user_id
 * @property Carbon $creation_date
 * 
 * @property User $user
 * @property Collection|CartItem[] $cart_items
 *
 * @package App\Models
 */
class Cart extends Model
{
	protected $table = 'cart';
	protected $primaryKey = 'cart_id';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'creation_date' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'creation_date'
	];

	public function user()
	{
		return $this->hasMany(User::class, 'user_id', 'user_id');
	}

	public function cart_items()
	{
		return $this->hasMany(CartItem::class);
	}
}
