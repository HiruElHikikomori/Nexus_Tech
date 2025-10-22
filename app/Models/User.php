<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * 
 * @property int $user_id
 * @property string $name
 * @property string $last_name
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $phone_number
 * @property string $address
 * @property string $profile_img_name
 * @property int $rol_id
 * 
 * @property Role $role
 * @property Collection|Cart[] $carts
 *
 * @package App\Models
 */
class User extends Authenticatable
{
	protected $table = 'users';
	protected $primaryKey = 'user_id';
	public $timestamps = false;

	protected $casts = [
		'rol_id' => 'int'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'name',
		'last_name',
		'username',
		'password',
		'email',
		'phone_number',
		'address',
		'profile_img_name',
		'rol_id'
	];

	public function role()
	{
		return $this->belongsTo(Role::class, 'rol_id');
	}

	public function carts()
	{
		return $this->belongsTo(Cart::class, 'cart_id');
	}

	//Hasheao automático para el password al registrar
	public function setPasswordAttribute($value){
		$this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
	}

	//Devolver la URL completa de la imagen de perfil
	public function getProfileImgUrlAttribute(){
		return $this->profile_img_name
			? url('img/users/' . $this->profile_img_name)
			: url('img/users/default.png');
	}
}
