<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\UserProduct;
use Illuminate\Http\Request;

class UserCatalogController extends Controller
{
    public function index(Request $r)
    {
        // 👇 ESTO YA LO TIENES
        $q   = $r->input('query');
        $pt  = $r->input('product_type_id');
        $min = $r->input('min_price');
        $max = $r->input('max_price');
        $sort= $r->input('sort_by_name');

        $items = UserProduct::query()
            ->with('type','user')
            ->when($q, fn($qq)=>$qq->where('name','like',"%{$q}%"))
            ->when($pt, fn($qq)=>$qq->where('product_type_id',$pt))
            ->when($min !== null && $min !== '', fn($qq)=>$qq->where('price','>=',(float)$min))
            ->when($max !== null && $max !== '', fn($qq)=>$qq->where('price','<=',(float)$max))
            ->when($sort === 'asc', fn($qq)=>$qq->orderBy('name','asc'))
            ->when($sort === 'desc', fn($qq)=>$qq->orderBy('name','desc'))
            ->orderByDesc('user_product_id')
            ->paginate(12)->appends($r->query());

        $productTypes = ProductType::all();
        $maxProductPrice = UserProduct::max('price');
        $maxProductPrice = $maxProductPrice ? max(100, ceil($maxProductPrice / 100) * 100) : 1000;

        return view('user_catalog.index', compact('items','productTypes','maxProductPrice'));
    }
}
