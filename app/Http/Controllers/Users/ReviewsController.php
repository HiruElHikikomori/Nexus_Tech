<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
    /**
     * POST /reviews
     * Crea una reseña si el usuario compró el artículo y no ha reseñado el mismo ítem antes.
     * La validación de que "sí compró" va en StoreReviewRequest + regla PurchasedItem.
     */
    public function store(StoreReviewRequest $request)
    {
        $userId = Auth::id();
        $data   = $request->validated();

        // Evitar reseña duplicada por usuario + item
        $exists = Review::where('user_id', $userId)
            ->when(!empty($data['product_id'] ?? null), function ($q) use ($data) {
                $q->where('product_id', $data['product_id']);
            })
            ->when(!empty($data['user_product_id'] ?? null), function ($q) use ($data) {
                $q->where('user_product_id', $data['user_product_id']);
            })
            ->exists();

        if ($exists) {
            return $this->respondBackOrJson(
                $request,
                ['review' => 'Ya dejaste una reseña para este artículo.'],
                null,
                422
            );
        }

        // Crear reseña (StoreReviewRequest ya validó TODO, incluyendo PurchasedItem)
        Review::create([
            'user_id'         => $userId,
            'product_id'      => $data['product_id']      ?? null,
            'user_product_id' => $data['user_product_id'] ?? null,
            'rating'          => $data['rating'],
            'comment'         => $data['comment'],
        ]);

        return $this->respondBackOrJson(
            $request,
            [],
            '¡Gracias por tu reseña!'
        );
    }

    /**
     * DELETE /reviews/{review}
     * Permite al usuario borrar su propia reseña.
     */
    public function destroy(Review $review, Request $request)
    {
        if ((int) $review->user_id !== (int) Auth::id()) {
            return $this->respondBackOrJson(
                $request,
                ['auth' => 'No puedes borrar esta reseña.'],
                null,
                403
            );
        }

        $review->delete();

        return $this->respondBackOrJson(
            $request,
            [],
            'Reseña eliminada.'
        );
    }

    /**
     * Respuesta consistente: JSON o redirect back con errores / success.
     */
    private function respondBackOrJson(
        Request $request,
        array $errors = [],
        ?string $successMsg = null,
        int $status = 200
    ) {
        // Petición AJAX / JSON
        if ($request->expectsJson()) {
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'errors'  => $errors,
                ], $status);
            }

            return response()->json([
                'success' => true,
                'message' => $successMsg,
            ], $status);
        }

        // Petición normal (navegador)
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        return back()->with('success', $successMsg);
    }
}
