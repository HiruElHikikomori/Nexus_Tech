@extends('layout.PlantillaUser')

@section('content')
<div class="container bg-accent1 m-auto rounded text-white p-4">
  <h3 class="mb-3">Mis piezas</h3>

  {{-- Alta rápida --}}
  <div class="card bg-secondary border-0 p-3 mb-4">
    <h6 class="mb-3">Publicar nueva pieza</h6>
    <form method="POST"
          action="{{ route('users.user_products.store', $routeUserId ?? (auth()->user()->user_id ?? 0)) }}"
          enctype="multipart/form-data"
          class="row g-3">
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

  @php
    $userId = $routeUserId ?? (auth()->user()->user_id ?? 0);
  @endphp

  {{-- Listado propio --}}
  @if(empty($items) || $items->isEmpty())
    <div class="alert alert-info">Aún no publicas piezas.</div>
  @else
    <div class="row">
      @foreach($items as $it)
        <div class="col-md-4 mb-3">
          <div class="card bg-secondary text-white h-100">
            <img src="{{ asset('img/user_products/' . ($it->img_name ?? 'default.png')) }}"
                 class="card-img-top"
                 style="height:180px;object-fit:cover">
            <div class="card-body d-flex flex-column">
              <h6 class="card-title mb-1">{{ $it->name }}</h6>

              <div class="small mb-2">
                <span class="badge bg-dark">{{ $it->type->name ?? '—' }}</span>
              </div>

              <div class="small mb-2">Stock: {{ $it->stock ?? 0 }}</div>
              <div class="fw-bold mb-3">$ {{ number_format($it->price ?? 0, 2) }}</div>

              <div class="d-flex justify-content-between mt-auto">
                {{-- Botón que abre el modal de edición --}}
                <button type="button"
                        class="btn btn-sm btn-outline-info"
                        data-bs-toggle="modal"
                        data-bs-target="#EditUserProduct{{ $it->user_product_id }}">
                  Editar
                </button>

                {{-- Eliminar --}}
                <form method="POST"
                      action="{{ route('users.user_products.destroy', [$userId, $it->user_product_id]) }}"
                      onsubmit="return confirm('¿Eliminar esta pieza?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </div>
            </div>
          </div>

          {{-- MODAL DE EDICIÓN --}}
          <div class="modal fade"
               id="EditUserProduct{{ $it->user_product_id }}"
               tabindex="-1"
               aria-labelledby="EditUserProductLabel{{ $it->user_product_id }}"
               aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content bg-secondary p-3 border-0">
                <div class="modal-header border-0">
                  <h3 class="modal-title" id="EditUserProductLabel{{ $it->user_product_id }}">
                    Editar pieza
                  </h3>
                  <button type="button"
                          class="btn-close"
                          data-bs-dismiss="modal"
                          aria-label="Close"></button>
                </div>

                <form action="{{ route('users.user_products.update', [$userId, $it->user_product_id]) }}"
                      method="POST"
                      enctype="multipart/form-data">
                  @csrf
                  @method('PUT')

                  <div class="modal-body bg-accent1 rounded border-0 p-4">
                    <div class="row">
                      {{-- Columna izquierda: imagen --}}
                      <div class="col-md-4 mb-3 border-0">
                        <img src="{{ asset('img/user_products/' . ($it->img_name ?? 'default.png')) }}"
                             class="img-fluid rounded mb-2"
                             style="max-width: 100%;">
                        <input type="file"
                               class="form-control border-0"
                               id="img_name_{{ $it->user_product_id }}"
                               name="img_name"
                               accept="image/*">
                        <small class="form-text text-muted">
                          Deja en blanco para mantener la imagen actual.
                        </small>
                        @error('img_name')
                          <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>

                      {{-- Columna derecha: formulario --}}
                      <div class="col-md-8 border-0">
                        <div class="mb-3">
                          <label for="name_{{ $it->user_product_id }}" class="form-label">
                            Nombre de la pieza:
                          </label>
                          <input type="text"
                                 class="form-control border-0"
                                 id="name_{{ $it->user_product_id }}"
                                 name="name"
                                 value="{{ old('name', $it->name) }}"
                                 required>
                          @error('name')
                            <div class="text-danger">{{ $message }}</div>
                          @enderror
                        </div>

                        <div class="mb-3">
                          <label for="product_type_id_{{ $it->user_product_id }}" class="form-label">
                            Tipo de pieza:
                          </label>
                          <select class="form-select border-0"
                                  id="product_type_id_{{ $it->user_product_id }}"
                                  name="product_type_id"
                                  required>
                            <option value="">Selecciona un tipo</option>
                            @foreach(($productTypes ?? []) as $type)
                              <option value="{{ $type->product_type_id }}"
                                {{ (old('product_type_id', $it->product_type_id ?? null) == $type->product_type_id) ? 'selected' : '' }}>
                                {{ $type->name }}
                              </option>
                            @endforeach
                          </select>
                          @error('product_type_id')
                            <div class="text-danger">{{ $message }}</div>
                          @enderror
                        </div>

                        <div class="mb-3">
                          <label for="description_{{ $it->user_product_id }}" class="form-label">
                            Descripción:
                          </label>
                          <textarea class="form-control border-0"
                                    id="description_{{ $it->user_product_id }}"
                                    name="description"
                                    rows="4"
                                    required>{{ old('description', $it->description) }}</textarea>
                          @error('description')
                            <div class="text-danger">{{ $message }}</div>
                          @enderror
                        </div>

                        <div class="mb-3">
                          <label for="condition_{{ $it->user_product_id }}" class="form-label">
                            Condición:
                          </label>
                          <input type="text"
                                 class="form-control border-0"
                                 id="condition_{{ $it->user_product_id }}"
                                 name="condition"
                                 value="{{ old('condition', $it->condition) }}"
                                 placeholder="Nuevo/Usado…">
                          @error('condition')
                            <div class="text-danger">{{ $message }}</div>
                          @enderror
                        </div>

                        <div class="mb-3">
                          <label for="price_{{ $it->user_product_id }}" class="form-label">
                            Precio:
                          </label>
                          <input type="number"
                                 step="0.01"
                                 class="form-control border-0"
                                 id="price_{{ $it->user_product_id }}"
                                 name="price"
                                 value="{{ old('price', $it->price) }}"
                                 required>
                          @error('price')
                            <div class="text-danger">{{ $message }}</div>
                          @enderror
                        </div>

                        <div class="mb-3">
                          <label for="stock_{{ $it->user_product_id }}" class="form-label">
                            Stock:
                          </label>
                          <input type="number"
                                 class="form-control border-0"
                                 id="stock_{{ $it->user_product_id }}"
                                 name="stock"
                                 value="{{ old('stock', $it->stock) }}"
                                 required>
                          @error('stock')
                            <div class="text-danger">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="modal-footer border-0">
                    <button type="submit"
                            class="btn btn-lg btn-outline-warning border-3 rounded">
                      <i class="bi bi-pencil-square"></i> Actualizar pieza
                    </button>

                    <button type="button"
                            class="btn btn-lg btn-primary rounded"
                            data-bs-dismiss="modal">
                      Cancelar
                    </button>
                  </div>
                </form>

              </div>
            </div>
          </div>
          {{-- FIN MODAL --}}
        </div>
      @endforeach
    </div>

    <div class="d-flex justify-content-center">
      {{ $items->links() }}
    </div>
  @endif
</div>
@endsection
