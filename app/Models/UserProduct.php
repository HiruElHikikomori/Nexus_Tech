<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProduct extends Model
{
    protected $table = 'user_products';
    protected $primaryKey = 'user_product_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'product_type_id',
        'name',
        'description',
        'price',
        'stock',
        'img_name',
        'condition',
        'report_count',
    ];

    /**
     * Dueño de la pieza (nombre original que ya tenías).
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Alias para poder usar with('user') sin errores.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Tipo de producto (CPU, GPU, etc.).
     */
    public function type()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id', 'product_type_id');
    }
}
