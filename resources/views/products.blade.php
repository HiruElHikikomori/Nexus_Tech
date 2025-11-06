@extends('layout.PlantillaUser')
@section('content')

{{-- er --}}
<div class="container bg-accent1 m-auto rounded text-white p-5">
    <h3>Filtro de búsqueda @if(request('query')) para "{{ request('query') }}" @endif</h3>


{{-- Bloque de filtros --}}
<div>
  <form action="{{ route('user.product') }}" method="GET" id="filterForm" class="text-white">
    <input type="hidden" name="query" value="{{ request('query') }}">

    <div class="row align-items-end">
      {{-- Tipo de producto --}}
      <div class="col col-md-4 col-lg-4">
        <label for="product_type" class="form-label">Por Tipo</label>
        <select class="form-select bg-primary border-primary" id="product_type" name="product_type_id">
          <option value="">Todos los Tipos</option>
          @foreach($productTypes as $type)
            <option value="{{ $type->product_type_id }}"
              {{ request('product_type_id') == $type->product_type_id ? 'selected' : '' }}>
              {{ $type->name }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Ordenar por nombre --}}
      <div class="col col-md-4 col-lg-3 " data-bs-theme="dark">
        <label for="sort_by_name" class="form-label">Ordenar por Nombre</label>
        <select class="form-select bg-primary border-primary" id="sort_by_name" name="sort_by_name">
          <option value="">Sin Orden</option>
          <option value="asc"  {{ request('sort_by_name') == 'asc'  ? 'selected' : '' }}>A-Z (Asc.)</option>
          <option value="desc" {{ request('sort_by_name') == 'desc' ? 'selected' : '' }}>Z-A (Desc.)</option>
        </select>
      </div>

      {{-- Rango de precio --}}
      <div class="col col-md-4 col-lg-5 text-center">
        <label class="form-label ">
           <h5 >Rango de Precio:</h5>


          <span>$</span><span class="ms-" id="minPriceDisplay">0</span> – $<span id="maxPriceDisplay">Máx</span>
        </label>
        <div id="price-slider"></div>

        <input type="hidden" name="min_price" id="hidden_min_price"
               value="{{ request('min_price', 0) }}">
        <input type="hidden" name="max_price" id="hidden_max_price"
               value="{{ request('max_price', $maxProductPrice) }}">
      </div>

      {{-- Botón --}}
      <div class="justify-content-center align-items-center mt-5">
        <button type="submit" class="btn btn-lg container-fluid rounded btn-accent2">Aplicar</button>
      </div>

    </div>

  </form>
</div>


    @if($products->isEmpty() && request('query'))
        <div class="alert alert-warning" role="alert">
            No se encontraron productos para tu búsqueda "{{ request('query') }}".
        </div>
    @elseif($products->isEmpty())
        <div class="alert alert-info" role="alert">
            No hay productos disponibles en este momento que coincidan con tus filtros.
        </div>
    @endif
</div>
<div class="container bg-accent1 rounded text-white p-5 my-5">
    <h1 class="text-white py-3">Lista de productos</h1>
    <div class="row py-2 m-auto">
        @foreach($products as $product)
        <div class="card mx-2 my-3 p-0 bg-secondary text-accent3 border-0" style="width: 18rem;">
            <img src="{{ asset('img/products/' . $product->img_name) }}" class="card-img-top" style="height:17rem">
            <div class="card-body text-center">
                <form class="add-to-cart-form" action="{{ route('cart.store') }}" method="POST">
                    <input type="hidden" name="product_id" value="{{ $product->products_id }}">
                    <input type="hidden" name="count" value="1">
                    <h5 class="card-title">{{$product->name}}</h5>
                    <p class="card-text">$ {{ number_format($product->price, 2) }}</p>
                    <button type="button" class="btn btn-lg rounded btn-outline-info " data-bs-toggle="modal" data-bs-target="#ShowProduct{{ $product->products_id }}"><i class="bi bi-card-list"></i></button>

                    @csrf
                    <button type="submit" class="btn btn-lg rounded btn-outline-success offset-md-4"><i class="bi bi-plus"></i></button>
                </form>

            </div>
        </div>

        {{-- Modal SHOW (dentro del bucle) --}}
            <div class="modal fade" id="ShowProduct{{ $product->products_id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content p-3 bg-accent1 border-0">
                        <div class="modal-header border border border-accent1">
                            <h3 class="modal-title" id="exampleModalLabel">Detalles de la pieza</h3>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body border-0">
                            <div class="card bg-secondary border-0 p-3">
                                <div class="card-body row container-fluid">

                                    <div class="col">
                                        <img class="img-fluid rounded border-0" src="{{ asset('img/products/' . $product->img_name) }}" style="height: 12rem;">
                                    </div>
                                    <div class="col-8">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">No. de producto:</label>
                                            <input type="text" class="form-control border-0" id="name" name="name" value="{{ $product->products_id }}" disabled required>
                                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nombre:</label>
                                            <input type="text" class="form-control border-0" id="name" name="name" value="{{ $product->name }}" disabled required>
                                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="name" class="form-label">Tipo:</label>
                                            <input type="text" class="form-control border-0" id="name" name="name" value="{{ $product->product_type->name ?? 'N/A' }}" disabled required>
                                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Descripción:</label>
                                            <textarea class="form-control border-0" id="exampleFormControlTextarea1" rows="3" disabled>{{ $product->description }}</textarea>
                                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Precio:</label>
                                            <input type="text" class="form-control border-0" id="name" name="name" value="$ {{ number_format($product->price, 2) }}" disabled required>
                                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Stock:</label>
                                            <input type="text" class="form-control border-0" id="name" name="name" value="{{ $product->stock }}" disabled required>
                                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                                    <x-reviews :itemType="'product'" :itemId="$product->products_id" />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border border-accent1">
                            <button type="button" class="btn btn-lg btn-primary rounded-pill" data-bs-dismiss="modal">Regresar</button>
                            <form class="add-to-cart-form" action="{{ route('cart.store') }}" method="POST">
                                <input type="hidden" name="product_id" value="{{ $product->products_id }}">
                                <input type="hidden" name="count" value="1">
                                @csrf
                                <button type="submit" class="btn btn-lg rounded-pill btn-outline-success"><i class="bi bi-plus"></i>Añadir al carrito</button>
                            </form>

                        </div>
                    </div>
                </div>

            </div>

        @endforeach
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showToast(message, type = 'info') {
        const toastElement = document.getElementById('liveToast');
        if (!toastElement) {
            console.error('Toast element not found. Please ensure #liveToast exists in your HTML.');
            alert(message);
            return;
        }
        const toast = new bootstrap.Toast(toastElement);
        const toastTitle = document.getElementById('toast-title');
        const toastBody = document.getElementById('toast-body');

        if (toastTitle) toastTitle.textContent = type.charAt(0).toUpperCase() + type.slice(1);
        if (toastBody) toastBody.textContent = message;

        toastElement.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
        switch (type) {
            case 'success':
                toastElement.classList.add('bg-success');
                break;
            case 'danger':
                toastElement.classList.add('bg-danger');
                break;
            case 'warning':
                toastElement.classList.add('bg-warning');
                break;
            case 'info':
            default:
                toastElement.classList.add('bg-info');
                break;
        }
        toast.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- noUiSlider Initialization ---
        const priceSlider = document.getElementById('price-slider');
        const minPriceDisplay = document.getElementById('minPriceDisplay');
        const maxPriceDisplay = document.getElementById('maxPriceDisplay');
        const hiddenMinPrice = document.getElementById('hidden_min_price');
        const hiddenMaxPrice = document.getElementById('hidden_max_price');

        // Get initial min and max price from hidden inputs (which hold values from request or defaults)
        const initialMin = parseFloat(hiddenMinPrice.value);
        const initialMax = parseFloat(hiddenMaxPrice.value);
        const maxProductPrice = parseFloat('{{ $maxProductPrice }}'); // Get max price passed from controller

        if (priceSlider) {
            noUiSlider.create(priceSlider, {
                start: [initialMin, initialMax], // Initial values for min and max
                connect: true, // Connect the handles
                range: {
                    'min': 0, // Minimum possible price
                    'max': maxProductPrice // Maximum price from your products
                },
                step: 1, // Increment step (e.g., allow only whole numbers for price)
                tooltips: true, // Show tooltips on handles
                format: { // Format for displaying numbers
                    to: function (value) {
                        return value.toFixed(0); // Display as whole numbers
                    },
                    from: function (value) {
                        return Number(value);
                    }
                }
            });

            // Update display spans and hidden inputs when slider values change
            priceSlider.noUiSlider.on('update', function (values, handle) {
                // values[0] is min, values[1] is max
                minPriceDisplay.textContent = values[0];
                maxPriceDisplay.textContent = values[1];
                hiddenMinPrice.value = values[0];
                hiddenMaxPrice.value = values[1];
            });

            // Optional: Submit form when slider changes (e.g., after user releases handle)
            // priceSlider.noUiSlider.on('change', function () {
            //     document.getElementById('filterForm').submit();
            // });
        }
        // --- End noUiSlider Initialization ---

        // Lógica existente para el carrito (mantener sin cambios)
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');

                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                axios.post(this.action, formData)
                    .then(response => {
                        if (response.data.success) {
                            showToast(response.data.message, 'success');
                        } else {
                            showToast('Error: ' + response.data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error al añadir al carrito:', error);
                        let errorMessage = 'Hubo un error al procesar tu solicitud.';
                        if (error.response && error.response.data && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        }
                        showToast(errorMessage, 'danger');
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="bi bi-plus"></i>';
                    });
            });
        });
    });
</script>
@endpush
