<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use App\Rules\PurchasedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    // POST /reviews
    public function store(StoreReviewRequest $request)
    {
        $userId = Auth::id();
        $data = $request->validated();

        // Validar que haya comprado (Regla personalizada)
        $rule = new PurchasedItem($userId, $data['product_id'] ?? null, $data['user_product_id'] ?? null);
        $validator = Validator::make(['check' => 1], ['check' => [$rule]]);
        if ($validator->fails()) {
            return $this->responseBackOrJson($request, ['item' => $validator->errors()->first()]);
        }

        // Evitar duplicado de reseña por user/item
        $exists = Review::where('user_id', $userId)
            ->when(!empty($data['product_id']), fn($q) => $q->where('product_id', $data['product_id']))
            ->when(!empty($data['user_product_id']), fn($q) => $q->where('user_product_id', $data['user_product_id']))
            ->exists();

        if ($exists) {
            return $this->responseBackOrJson($request, ['review' => 'Ya dejaste una reseña para este artículo.']);
        }

        Review::create([
            'user_id'         => $userId,
            'product_id'      => $data['product_id'] ?? null,
            'user_product_id' => $data['user_product_id'] ?? null,
            'rating'          => $data['rating'],
            'comment'         => $data['comment'],
        ]);

        return $this->responseBackOrJson($request, [], '¡Gracias por tu reseña!');
    }

    // opcional: borrar reseña propia
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return back()->withErrors(['auth' => 'No puedes borrar esta reseña.']);
        }
        $review->delete();
        return back()->with('success', 'Reseña eliminada.');
    }

    private function responseBackOrJson(Request $request, array $errors = [], string $successMsg = null)
    {
        if ($request->expectsJson()) {
            if ($errors) return response()->json(['success' => false, 'errors' => $errors], 422);
            return response()->json(['success' => true, 'message' => $successMsg]);
        }
        if ($errors) return back()->withErrors($errors)->withInput();
        return back()->with('success', $successMsg);
    }
}
