<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\UserProduct;
use App\Models\ProductType;
use Illuminate\Http\Request;

class UserCatalogController extends Controller
{
    public function index(Request $r)
    {
        $q    = $r->input('query');
        $pt   = $r->input('product_type_id');
        $min  = $r->input('min_price');
        $max  = $r->input('max_price');
        $sort = $r->input('sort_by_name');

        $items = UserProduct::query()
            ->with(['type', 'owner']) // type = categoría, owner = vendedor
            ->when($q, function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%");
            })
            ->when($pt, function ($qq) use ($pt) {
                $qq->where('product_type_id', $pt);
            })
            ->when($min !== null && $min !== '', function ($qq) use ($min) {
                $qq->where('price', '>=', (float) $min);
            })
            ->when($max !== null && $max !== '', function ($qq) use ($max) {
                $qq->where('price', '<=', (float) $max);
            })
            ->when($sort === 'asc',  fn($qq) => $qq->orderBy('name', 'asc'))
            ->when($sort === 'desc', fn($qq) => $qq->orderBy('name', 'desc'))
            ->orderByDesc('user_product_id')
            ->paginate(12)
            ->appends($r->query());

        // Tipos de producto (para filtros en el catálogo)
        $productTypes = ProductType::all();

        // Precio máximo dinámico para el range de precio
        $maxProductPrice = UserProduct::max('price');
        $maxProductPrice = $maxProductPrice
            ? max(100, ceil($maxProductPrice / 100) * 100)
            : 1000;

        return view('user_catalog.index', compact('items', 'productTypes', 'maxProductPrice'));
    }
}
