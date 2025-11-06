@props([
    'itemType' => 'product', // 'product' o 'user_product'
    'itemId'   => null,
])

@php
    // Clave foránea según el tipo de item
    $fk = $itemType === 'user_product' ? 'user_product_id' : 'product_id';

    // Reseñas del producto/pieza
    $reviews = \App\Models\Review::with('user')
        ->where($fk, $itemId)
        ->latest('review_id')
        ->get();

    $avg = $reviews->avg('rating');

    // ¿El usuario autenticado ha comprado este item?
    $canReview = false;
    $userId = auth()->id();

    if ($userId && $itemId) {
        $query = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'orders.order_id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->whereIn('orders.status', ['paid', 'completed', 'simulated']);

        if ($itemType === 'user_product') {
            $query->where('order_items.user_product_id', $itemId);
        } else {
            $query->where('order_items.product_id', $itemId);
        }

        $canReview = $query->exists();
    }
@endphp

<div class="mt-4">
  <div class="d-flex align-items-center gap-2 mb-2">
    <h6 class="mb-0">Reseñas</h6>
    <span class="badge bg-dark">Promedio: {{ $avg ? number_format($avg, 1) : '—' }}/5</span>
    <span class="text-muted small">({{ $reviews->count() }})</span>
  </div>

  {{-- FORMULARIO: solo si está logueado Y sí ha comprado el producto --}}
  @auth
    @if($canReview)
      <form method="POST" action="{{ route('reviews.store') }}" class="row g-2 mb-3">
        @csrf
        <input type="hidden" name="{{ $fk }}" value="{{ $itemId }}">

        <div class="col-md-2">
          <select name="rating" class="form-select bg-primary border-primary text-white" required>
            <option value="5">5 ★</option>
            <option value="4">4 ★</option>
            <option value="3">3 ★</option>
            <option value="2">2 ★</option>
            <option value="1">1 ★</option>
          </select>
        </div>

        <div class="col-md-8">
          <input
            name="comment"
            class="form-control bg-primary border-primary text-white"
            placeholder="Escribe tu reseña…"
            required
          >
        </div>

        <div class="col-md-2 d-grid">
          <button type="submit" class="btn btn-outline-success">Publicar</button>
        </div>
      </form>
    @endif
  @endauth

  {{-- LISTA DE RESEÑAS --}}
  @forelse($reviews as $rv)
    <div class="border-top border-accent1 py-2">
      <div class="d-flex justify-content-between">
        <div class="small">
          <b>{{ $rv->user->name ?? 'Usuario' }}</b>
          <span class="ms-2">★ {{ $rv->rating }}/5</span>
        </div>
        @auth
          {{-- Botón eliminar solo para el dueño de la reseña --}}
          @if((int) $rv->user_id === (int) auth()->id())
            <form method="POST" action="{{ route('reviews.destroy', $rv->review_id) }}">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Eliminar</button>
            </form>
          @endif
        @endauth
      </div>
      <div class="small mt-1">{{ $rv->comment }}</div>
    </div>
  @empty
    <div class="text-muted small">Aún no hay reseñas.</div>
  @endforelse
</div>
