@extends('layout.PlantillaAdmin')

@section('content')

<div class="container-fluid flex-column justify-content-center align-items-center mb-5"
     style="min-height: 100vh; font-family: roboto;">
    <br><br>
    <div class="container bg-accent1 card p-5">

        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Piezas de usuario</h1>
                <p class="mb-0 text-white-50">
                    Usuario: <strong>{{ $user->username }}</strong> (ID: {{ $user->user_id }})
                </p>
            </div>

            <a href="{{ route('admin.users.index') }}"
               class="btn btn-outline-light rounded">
                <i class="bi bi-arrow-left"></i> Volver a lista de usuarios
            </a>
        </div>

        {{-- Mensajes de éxito --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabla de piezas --}}
@if($items->isEmpty())
    <div class="alert alert-secondary mb-0">
        <strong>Este usuario no ha publicado ninguna pieza.</strong>
    </div>
@else
    <div class="table-responsive">
        <table class="table card-table mb-0" style="border-collapse: separate; border-spacing: 0;">
            <thead class="text-center">
                <tr>
                    <th class="bg-dark border border-primary" style="border-top-left-radius:1.2rem">ID</th>
                    <th class="bg-dark border border-primary">Imagen</th>
                    <th class="bg-dark border border-primary">Nombre</th>
                    <th class="bg-dark border border-primary">Tipo</th>
                    <th class="bg-dark border border-primary">Precio</th>
                    <th class="bg-dark border border-primary">Stock</th>
                    <th class="bg-dark border border-primary">Estado</th>
                    <th class="bg-dark border border-primary">Reportes</th>
                    <th class="bg-dark border border-primary" style="border-top-right-radius:1.2rem">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach ($items as $item)
                    <tr>
                        <td class="bg-secondary border border-primary">
                            {{ $item->user_product_id }}
                        </td>
                        <td class="bg-secondary border border-primary">
                            <img src="{{ asset('img/user_products/' . $item->img_name) }}"
                                 alt="Imagen pieza"
                                 class="img-fluid rounded"
                                 style="max-height: 60px;">
                        </td>
                        <td class="bg-secondary border border-primary">
                            {{ $item->name }}
                        </td>
                        <td class="bg-secondary border border-primary">
                            {{ $item->type->name ?? 'N/A' }}
                        </td>
                        <td class="bg-secondary border border-primary">
                            ${{ number_format($item->price, 2) }}
                        </td>
                        <td class="bg-secondary border border-primary">
                            {{ $item->stock }}
                        </td>
                        <td class="bg-secondary border border-primary">
                            {{ $item->condition ?? 'Sin especificar' }}
                        </td>
                        <td class="bg-secondary border border-primary">
                            {{ $item->report_count ?? 0 }}
                        </td>
                        <td class="bg-secondary border border-primary">
                            {{-- Ver Detalles --}}
                            <button type="button"
                                    class="btn btn-outline-info btn-sm rounded mb-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ShowUserProduct{{ $item->user_product_id }}">
                                <i class="bi bi-card-list"></i> Ver
                            </button>

                            {{-- Eliminar --}}
                            <button type="button"
                                    class="btn btn-outline-danger btn-sm rounded mb-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#DeleteUserProduct{{ $item->user_product_id }}">
                                <i class="bi bi-trash-fill"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                @endforeach

                {{-- fila decorativa final --}}
                <tr class="text-center bg-dark">
                    <td class="border-primary" style="border-bottom-left-radius:1.3rem"></td>
                    <td class="border-primary"></td>
                    <td class="border-primary"></td>
                    <td class="border-primary"></td>
                    <td class="border-primary"></td>
                    <td class="border-primary"></td>
                    <td class="border-primary"></td>
                    <td class="border-primary"></td>
                    <td class="border-primary" style="border-bottom-right-radius:1.3rem"></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-3">
        {{ $items->links('vendor.pagination.bootstrap-5') }}
    </div>
@endif

{{-- ================= MODALES FUERA DE LA TABLA ================= --}}
@foreach ($items as $item)

    {{-- Modal: Ver detalles de pieza + reportes --}}
    <div class="modal fade"
        id="ShowUserProduct{{ $item->user_product_id }}"
        tabindex="-1"
        aria-labelledby="ShowUserProductLabel{{ $item->user_product_id }}"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3 bg-accent1 border-0">
                <div class="modal-header border border-accent1">
                    <h3 class="modal-title"
                        id="ShowUserProductLabel{{ $item->user_product_id }}">
                        Detalles de la pieza de usuario
                    </h3>
                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body border-0">
                    <div class="card bg-secondary border-0 p-3">
                        <div class="card-body row container-fluid">

                            {{-- Columna imagen --}}
                            <div class="col-md-4 mb-3">
                                <img class="img-fluid rounded border-0"
                                    src="{{ asset('img/user_products/' . $item->img_name) }}"
                                    style="height: auto;">
                            </div>

                            {{-- Columna datos principales --}}
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">ID de pieza:</label>
                                    <input type="text"
                                        class="form-control border-0"
                                        value="{{ $item->user_product_id }}"
                                        disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nombre:</label>
                                    <input type="text"
                                        class="form-control border-0"
                                        value="{{ $item->name }}"
                                        disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tipo:</label>
                                    <input type="text"
                                        class="form-control border-0"
                                        value="{{ $item->type->name ?? 'N/A' }}"
                                        disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descripción:</label>
                                    <textarea class="form-control border-0"
                                            rows="4"
                                            disabled>{{ $item->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Precio:</label>
                                    <input type="text"
                                        class="form-control border-0"
                                        value="$ {{ number_format($item->price, 2) }}"
                                        disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stock:</label>
                                    <input type="text"
                                        class="form-control border-0"
                                        value="{{ $item->stock }}"
                                        disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Condición:</label>
                                    <input type="text"
                                        class="form-control border-0"
                                        value="{{ $item->condition ?? 'Sin especificar' }}"
                                        disabled>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Total de reportes:</label>
                                    <input type="text"
                                        class="form-control border-0"
                                        value="{{ $item->report_count ?? 0 }}"
                                        disabled>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ===================== Reportes sobre esta pieza ===================== --}}
                    <div class="card bg-secondary border-0 p-3 mt-3">
                        <h5 class="text-white mb-3">
                            Reportes sobre esta pieza
                        </h5>

                        @if($item->reports->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-sm mb-0 text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-white">#</th>
                                            <th class="text-white">Reportado por</th>
                                            <th class="text-white">Motivo</th>
                                            <th class="text-white">Estado</th>
                                            <th class="text-white">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->reports as $report)
                                            <tr>
                                                <td class="text-white">
                                                    {{ $report->report_id }}
                                                </td>
                                                <td class="text-white">
                                                    {{ $report->reporter->username ?? 'Usuario desconocido' }}
                                                </td>
                                                <td class="text-white text-start">
                                                    {{ $report->reason }}
                                                </td>
                                                <td class="text-white">
                                                    {{ ucfirst($report->status) }}
                                                </td>
                                                <td class="text-white-50">
                                                    {{ optional($report->created_at)->format('d/m/Y H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-white-50 mb-0">
                                Esta pieza aún no ha recibido ningún reporte.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="modal-footer border border-accent1">
                    <button type="button"
                            class="btn btn-lg btn-primary rounded-pill"
                            data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Eliminar pieza --}}
    <div class="modal fade"
        id="DeleteUserProduct{{ $item->user_product_id }}"
        tabindex="-1"
        aria-labelledby="DeleteUserProductLabel{{ $item->user_product_id }}"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-accent1 border-0 p-3">
                <div class="modal-header border-0">
                    <h3 class="modal-title"
                        id="DeleteUserProductLabel{{ $item->user_product_id }}">
                        ¿Estás seguro que quieres eliminar esta pieza?
                    </h3>
                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.user_products.destroy', $item->user_product_id) }}"
                        method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <div class="card bg-secondary border-0 p-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <img src="{{ asset('img/user_products/' . $item->img_name) }}"
                                            class="img-fluid rounded"
                                            style="max-width: 100%;">
                                    </div>
                                    <div class="col-md-8 text-white">
                                        <div class="mb-3">
                                            <label class="form-label">ID de pieza:</label>
                                            <input type="text"
                                                class="form-control border-0"
                                                value="{{ $item->user_product_id }}"
                                                disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre:</label>
                                            <input type="text"
                                                class="form-control border-0"
                                                value="{{ $item->name }}"
                                                disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tipo:</label>
                                            <input type="text"
                                                class="form-control border-0"
                                                value="{{ $item->type->name ?? 'N/A' }}"
                                                disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Precio:</label>
                                            <input type="text"
                                                class="form-control border-0"
                                                value="$ {{ number_format($item->price, 2) }}"
                                                disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Stock:</label>
                                            <input type="text"
                                                class="form-control border-0"
                                                value="{{ $item->stock }}"
                                                disabled>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label">Condición:</label>
                                            <input type="text"
                                                class="form-control border-0"
                                                value="{{ $item->condition ?? 'Sin especificar' }}"
                                                disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="submit"
                                class="btn btn-lg btn-outline-danger border-3 rounded">
                            <i class="bi bi-trash-fill"></i> <b>Eliminar</b>
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

@endforeach




    </div>
</div>

@endsection
