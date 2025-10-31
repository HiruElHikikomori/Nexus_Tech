@extends('layout.PlantillaUser')

@section('content')
<div class="container bg-accent1 m-auto rounded text-white p-4">
  <h3 class="mb-3">Mis piezas</h3>

  {{-- Alta rápida --}}
  <div class="card bg-secondary border-0 p-3 mb-4">
    <h6 class="mb-3">Publicar nueva pieza</h6>
    <form method="POST" action="{{ route('users.user_products.store', $routeUserId ?? (auth()->user()->user_id ?? 0)) }}" enctype="multipart/form-data" class="row g-3">
      @csrf
      <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input name="name" class="form-control bg-primary border-primary text-white" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Tipo</label>
        <select name="product_type_id" class="form-select bg-primary border-primary text-white" required>
          <option value="">Selecciona…</option>
          @foreach(($productTypes ?? []) as $t)
            <option value="{{ $t->product_type_id }}">{{ $t->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Precio</label>
        <input type="number" name="price" min="0" step="0.01" class="form-control bg-primary border-primary text-white" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" min="1" step="1" class="form-control bg-primary border-primary text-white" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Condición</label>
        <input name="condition" class="form-control bg-primary border-primary text-white" placeholder="Nuevo/Usado…">
      </div>
      <div class="col-md-6">
        <label class="form-label">Imagen</label>
        <input type="file" name="img_name" accept="image/*" class="form-control">
      </div>
      <div class="col-12">
        <label class="form-label">Descripción</label>
        <textarea name="description" rows="3" class="form-control bg-primary border-primary text-white"></textarea>
      </div>
      <div class="col-12 text-end">
        <button class="btn btn-outline-success">Publicar</button>
      </div>
    </form>
  </div>

  {{-- Listado propio --}}
  @if(empty($items) || $items->isEmpty())
    <div class="alert alert-info">Aún no publicas piezas.</div>
  @else
    <div class="row">
      @foreach($items as $it)
        <div class="col-md-4 mb-3">
          <div class="card bg-secondary text-white h-100">
            <img src="{{ asset('img/user_products/' . ($it->img_name ?? 'default.png')) }}" class="card-img-top" style="height:180px;object-fit:cover">
            <div class="card-body d-flex flex-column">
              <h6 class="card-title mb-1">{{ $it->name }}</h6>
              <div class="small mb-2">
                <span class="badge bg-dark">{{ $it->type->name ?? '—' }}</span>
              </div>
              <div class="small mb-2">Stock: {{ $it->stock ?? 0 }}</div>
              <div class="fw-bold mb-3">$ {{ number_format($it->price ?? 0, 2) }}</div>
              <div class="d-flex justify-content-between mt-auto">
                <a class="btn btn-sm btn-outline-info disabled">Editar</a>
                <form method="POST" action="{{ route('users.user_products.destroy', [($routeUserId ?? (auth()->user()->user_id ?? 0)), $it->user_product_id]) }}" onsubmit="return confirm('¿Eliminar esta pieza?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="d-flex justify-content-center">
      {{ $items->links() }}
    </div>
  @endif
</div>
@endsection
