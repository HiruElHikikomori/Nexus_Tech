<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'report_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'product_id',
        'user_product_id',
        'reported_user_id',
        'reason',
        'status',
        'resolved_by',
        'resolved_at',
    ];

    // users.user_id (INT firmado)
    public function reporter()     { return $this->belongsTo(User::class, 'user_id', 'user_id'); }
    public function reportedUser() { return $this->belongsTo(User::class, 'reported_user_id', 'user_id'); }
    public function resolver()     { return $this->belongsTo(User::class, 'resolved_by', 'user_id'); }

    // products.products_id (INT firmado)
    public function product()      { return $this->belongsTo(Product::class, 'product_id', 'products_id'); }

    // user_products.user_product_id (UNSIGNED)
    public function userProduct()  { return $this->belongsTo(UserProduct::class, 'user_product_id', 'user_product_id'); }
}
