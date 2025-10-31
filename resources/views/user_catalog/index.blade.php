@extends('layout.PlantillaUser')

@section('content')
<div class="container bg-accent1 m-auto rounded text-white p-4">
  <h3 class="mb-3">Catálogo de piezas de usuarios</h3>

  <form action="{{ route('user_catalog.index') }}" method="GET" class="row g-2 align-items-end">
    <div class="col-md-4">
      <label class="form-label">Buscar</label>
      <input type="text" class="form-control bg-primary border-primary text-white" name="query" value="{{ request('query') }}" placeholder="Nombre, descripción…">
    </div>
    <div class="col-md-4">
      <label class="form-label">Tipo</label>
      <select name="product_type_id" class="form-select bg-primary border-primary text-white" onchange="this.form.submit()">
        <option value="">Todos</option>
        @foreach(($productTypes ?? []) as $t)
          <option value="{{ $t->product_type_id }}" {{ request('product_type_id') == $t->product_type_id ? 'selected' : '' }}>
            {{ $t->name }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4 text-end">
      <button class="btn btn-outline-light">Aplicar</button>
      <a href="{{ route('user_catalog.index') }}" class="btn btn-outline-secondary">Limpiar</a>
    </div>
  </form>
</div>

<div class="container bg-accent1 rounded text-white p-4 my-4">
  @if(empty($items) || $items->isEmpty())
    <div class="alert alert-info mb-0">No hay piezas que coincidan con los filtros.</div>
  @else
    <div class="row">
      @foreach($items as $it)
        <div class="col-md-3 mb-3">
          <div class="card bg-secondary text-white h-100 border-0">
            <img src="{{ asset('img/user_products/' . ($it->img_name ?? 'default.png')) }}" class="card-img-top" style="height:170px;object-fit:cover">
            <div class="card-body d-flex flex-column">
              <h6 class="card-title mb-1">{{ $it->name }}</h6>
              <div class="small mb-2">
                <span class="badge bg-dark">{{ $it->type->name ?? 'Sin tipo' }}</span>
              </div>
              <div class="fw-bold mb-2">$ {{ number_format($it->price ?? 0, 2) }}</div>
              <button class="btn btn-outline-info mt-auto" data-bs-toggle="modal" data-bs-target="#uitem-{{ $it->user_product_id }}">Ver</button>
            </div>
          </div>
        </div>

        {{-- Modal de detalles --}}
        <div class="modal fade" id="uitem-{{ $it->user_product_id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-secondary text-white border border-accent1">
              <div class="modal-header">
                <h5 class="modal-title">{{ $it->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-5">
                    <img class="img-fluid rounded" src="{{ asset('img/user_products/' . ($it->img_name ?? 'default.png')) }}">
                  </div>
                  <div class="col-md-7">
                    <p class="mb-2">{{ $it->description ?? 'Sin descripción' }}</p>
                    <p class="mb-1"><b>Tipo:</b> {{ $it->type->name ?? '—' }}</p>
                    <p class="mb-1"><b>Condición:</b> {{ $it->condition ?? '—' }}</p>
                    <p class="mb-1"><b>Stock:</b> {{ $it->stock ?? 0 }}</p>
                    <p class="mb-2"><b>Vendedor:</b> {{ $it->owner->name ?? '—' }}</p>
                    <h4 class="mb-0">$ {{ number_format($it->price ?? 0, 2) }}</h4>
                  </div>
                </div>

                {{-- Reseñas de esta pieza de usuario --}}
                <x-reviews :itemType="'user_product'" :itemId="$it->user_product_id" />
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="d-flex justify-content-center mt-2">
      {{ $items->links() }}
    </div>
  @endif
</div>
@endsection
