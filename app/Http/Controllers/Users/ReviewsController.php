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

    /**
     * POST /reviews
     * Crea una reseña si el usuario compró el artículo y no ha reseñado el mismo ítem antes.
     */
    public function store(StoreReviewRequest $request)
    {
        $userId = Auth::id();
        $data   = $request->validated();

        // 1) Regla: solo puede reseñar si lo compró (orders + order_items)
        $rule = new PurchasedItem(
            $userId,
            $data['product_id'] ?? null,
            $data['user_product_id'] ?? null
        );

        $validator = Validator::make(['check' => 1], ['check' => [$rule]]);
        if ($validator->fails()) {
            return $this->responseBackOrJson(
                $request,
                ['item' => $validator->errors()->first()],
                null,
                422
            );
        }

        // 2) Evitar duplicado de reseña por usuario + item (product_id XOR user_product_id)
        $exists = Review::where('user_id', $userId)
            ->when(!empty($data['product_id']), fn($q) => $q->where('product_id', $data['product_id']))
            ->when(!empty($data['user_product_id']), fn($q) => $q->where('user_product_id', $data['user_product_id']))
            ->exists();

        if ($exists) {
            return $this->responseBackOrJson(
                $request,
                ['review' => 'Ya dejaste una reseña para este artículo.'],
                null,
                422
            );
        }

        // 3) Crear reseña
        Review::create([
            'user_id'         => $userId,
            'product_id'      => $data['product_id'] ?? null,
            'user_product_id' => $data['user_product_id'] ?? null,
            'rating'          => $data['rating'],
            'comment'         => $data['comment'],
        ]);

        return $this->responseBackOrJson(
            $request,
            [],
            '¡Gracias por tu reseña!'
        );
    }

    /**
     * DELETE /reviews/{review}
     * Permite al usuario borrar su propia reseña.
     */
    public function destroy(Review $review)
    {
        if ((int)$review->user_id !== (int)Auth::id()) {
            return back()->withErrors(['auth' => 'No puedes borrar esta reseña.']);
        }

        $review->delete();
        return back()->with('success', 'Reseña eliminada.');
    }

    /**
     * Respuesta consistente: JSON (200/4xx) o redirect back con errores/success.
     */
    private function responseBackOrJson(Request $request, array $errors = [], ?string $successMsg = null, int $status = 200)
    {
        if ($request->expectsJson()) {
            if (!empty($errors)) {
                return response()->json(['success' => false, 'errors' => $errors], $status);
            }
            return response()->json(['success' => true, 'message' => $successMsg], $status);
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        return back()->with('success', $successMsg);
    }
}
