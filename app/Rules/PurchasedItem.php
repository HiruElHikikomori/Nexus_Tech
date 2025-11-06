<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchasedItem implements Rule
{
    /**
     * @var string 'product' | 'user_product'
     */
    protected string $mode;

    /**
     * @param string $mode 'product' para products_id, 'user_product' para user_product_id
     */
    public function __construct(string $mode = 'product')
    {
        $this->mode = $mode;
    }

    /**
     * Determina si el usuario autenticado ha comprado el ítem indicado.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $userId = Auth::id();

        if (!$userId) {
            // La ruta ya está protegida con auth, pero por si acaso
            return false;
        }

        if (empty($value)) {
            // La parte de required_without/prohibits se encarga de esto
            return false;
        }

        $column = $this->mode === 'user_product'
            ? 'order_items.user_product_id'
            : 'order_items.product_id';

        return DB::table('order_items')
            ->join('orders', 'orders.order_id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->whereIn('orders.status', ['paid', 'completed', 'simulated']) // estados válidos
            ->where($column, $value)
            ->exists();
    }

    /**
     * Mensaje de error por defecto.
     */
    public function message(): string
    {
        return 'Solo puedes reseñar productos que hayas comprado.';
    }
}
