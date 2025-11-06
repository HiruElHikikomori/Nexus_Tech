@extends('layout.PlantillaUser')
@section('content')

{{-- Filtros --}}
<div class="container bg-accent1 m-auto rounded text-white p-5">
    <h3>Filtro de búsqueda @if(request('query')) para "{{ request('query') }}" @endif</h3>

    {{-- Bloque de filtros --}}
    <div>
        <form action="{{ route('user_catalog.index') }}" method="GET" id="filterForm" class="text-white">
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
                <div class="col col-md-4 col-lg-3" data-bs-theme="dark">
                    <label for="sort_by_name" class="form-label">Ordenar por Nombre</label>
                    <select class="form-select bg-primary border-primary" id="sort_by_name" name="sort_by_name">
                        <option value="">Sin Orden</option>
                        <option value="asc"  {{ request('sort_by_name') == 'asc'  ? 'selected' : '' }}>A-Z (Asc.)</option>
                        <option value="desc" {{ request('sort_by_name') == 'desc' ? 'selected' : '' }}>Z-A (Desc.)</option>
                    </select>
                </div>

                {{-- Rango de precio --}}
                <div class="col col-md-4 col-lg-5 text-center">
                    <label class="form-label">
                        <h5>Rango de Precio:</h5>
                        <span>$</span><span id="minPriceDisplay">0</span> – $<span id="maxPriceDisplay">Máx</span>
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

    @if($items->isEmpty() && request('query'))
        <div class="alert alert-warning mt-3" role="alert">
            No se encontraron piezas para tu búsqueda "{{ request('query') }}".
        </div>
    @elseif($items->isEmpty())
        <div class="alert alert-info mt-3" role="alert">
            No hay piezas disponibles en este momento que coincidan con tus filtros.
        </div>
    @endif
</div>

{{-- Lista de productos (piezas de usuario) --}}
<div class="container bg-accent1 rounded text-white p-5 my-5">
    <h1 class="text-white py-3">Piezas de segunda mano</h1>
    <div class="row py-2 m-auto">
        @foreach($items as $item)
            <div class="card mx-2 my-3 p-0 bg-secondary text-accent3 border-0" style="width: 18rem;">
                <img src="{{ asset('img/user_products/' . $item->img_name) }}" class="card-img-top" style="height:17rem; object-fit: cover;">
                <div class="card-body text-center">

                    {{-- 🔹 MISMO FORMATO QUE EL CATÁLOGO OFICIAL, PERO PARA UserProduct --}}
                    <form class="add-to-cart-form" action="{{ route('cart.store') }}" method="POST">
                        {{-- IMPORTANTE: este campo lo usará el backend para saber que es una pieza de usuario --}}
                        <input type="hidden" name="user_product_id" value="{{ $item->user_product_id }}">
                        <input type="hidden" name="count" value="1">

                        <h5 class="card-title">{{ $item->name }}</h5>
                        <p class="card-text">$ {{ number_format($item->price, 2) }}</p>

                        {{-- Ver detalles (modal) --}}
                        <button type="button"
                                class="btn btn-lg rounded btn-outline-info"
                                data-bs-toggle="modal"
                                data-bs-target="#ShowUserProduct{{ $item->user_product_id }}">
                            <i class="bi bi-card-list"></i>
                        </button>

                        @csrf
                        {{-- Botón añadir al carrito (tarjeta) --}}
                        <button type="submit" class="btn btn-lg rounded btn-outline-success offset-md-4">
                            <i class="bi bi-plus"></i>
                        </button>
                    </form>

                </div>
            </div>

            {{-- Modal SHOW (dentro del bucle) --}}
            <div class="modal fade"
                 id="ShowUserProduct{{ $item->user_product_id }}"
                 tabindex="-1"
                 aria-labelledby="userProductLabel{{ $item->user_product_id }}"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content p-3 bg-accent1 border-0">
                        <div class="modal-header border border-accent1">
                            <h3 class="modal-title" id="userProductLabel{{ $item->user_product_id }}">Detalles de la pieza</h3>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body border-0">
                            <div class="card bg-secondary border-0 p-3">
                                <div class="card-body row container-fluid">

                                    <div class="col">
                                        <img class="img-fluid rounded border-0"
                                             src="{{ asset('img/user_products/' . $item->img_name) }}"
                                             style="height: 12rem; object-fit: cover;">
                                    </div>

                                    <div class="col-8">
                                        <div class="mb-3">
                                            <label class="form-label">No. de pieza:</label>
                                            <input type="text" class="form-control border-0" value="{{ $item->user_product_id }}" disabled>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Nombre:</label>
                                            <input type="text" class="form-control border-0" value="{{ $item->name }}" disabled>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Tipo:</label>
                                            <input type="text" class="form-control border-0" value="{{ $item->type->name ?? 'N/A' }}" disabled>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Descripción:</label>
                                            <textarea class="form-control border-0" rows="3" disabled>{{ $item->description }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Precio:</label>
                                            <input type="text" class="form-control border-0" value="$ {{ number_format($item->price, 2) }}" disabled>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Stock:</label>
                                            <input type="text" class="form-control border-0" value="{{ $item->stock }}" disabled>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Estado:</label>
                                            <input type="text" class="form-control border-0" value="{{ $item->condition ?? 'Sin especificar' }}" disabled>
                                        </div>
                                    </div>

                                    {{-- 🔹 Reseñas para piezas de usuario --}}
                                    <x-reviews :itemType="'user_product'" :itemId="$item->user_product_id" />
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border border-accent1">
                            <button type="button" class="btn btn-lg btn-primary rounded-pill" data-bs-dismiss="modal">
                                Regresar
                            </button>

                            {{-- Botón de comprar dentro del modal, igual que en el catálogo oficial --}}
                            <form class="add-to-cart-form" action="{{ route('cart.store') }}" method="POST">
                                <input type="hidden" name="user_product_id" value="{{ $item->user_product_id }}">
                                <input type="hidden" name="count" value="1">
                                @csrf
                                <button type="submit" class="btn btn-lg rounded-pill btn-outline-success">
                                    <i class="bi bi-plus"></i> Añadir al carrito
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach
    </div>

    {{-- Paginación --}}
    <div class="mt-3">
        {{ $items->links('vendor.pagination.bootstrap-5') }}
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

        const initialMin = parseFloat(hiddenMinPrice.value);
        const initialMax = parseFloat(hiddenMaxPrice.value);
        const maxProductPrice = parseFloat('{{ $maxProductPrice }}');

        if (priceSlider) {
            noUiSlider.create(priceSlider, {
                start: [initialMin, initialMax],
                connect: true,
                range: {
                    'min': 0,
                    'max': maxProductPrice
                },
                step: 1,
                tooltips: true,
                format: {
                    to: function (value) {
                        return value.toFixed(0);
                    },
                    from: function (value) {
                        return Number(value);
                    }
                }
            });

            priceSlider.noUiSlider.on('update', function (values) {
                minPriceDisplay.textContent = values[0];
                maxPriceDisplay.textContent = values[1];
                hiddenMinPrice.value = values[0];
                hiddenMaxPrice.value = values[1];
            });
        }

        // 🔹 MISMA LÓGICA DE CARRITO QUE EN EL CATÁLOGO OFICIAL
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
