<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class PurchasedItem implements ValidationRule
{
    protected int $userId;
    protected ?int $productId;
    protected ?int $userProductId;

    public function __construct(int $userId, ?int $productId = null, ?int $userProductId = null)
    {
        $this->userId = $userId;
        $this->productId = $productId;
        $this->userProductId = $userProductId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $q = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->where('orders.user_id', $this->userId);

        if ($this->productId) {
            $q->where('order_items.product_id', $this->productId);
        }
        if ($this->userProductId) {
            $q->where('order_items.user_product_id', $this->userProductId);
        }

        if (!$q->exists()) {
            $fail('Solo puedes reseñar artículos que hayas comprado.');
        }
    }
}
