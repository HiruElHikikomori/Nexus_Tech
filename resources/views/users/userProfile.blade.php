@extends('layout.PlantillaUser')
@section('content')

<div class="card p-5 container bg-accent1 mb-4">
    <h1 class="text-white">Perfil de: {{ $user->username }}</h1>
    <div class="card-body mt-0">
        <div class="row">
            {{-- Columna izquierda --}}
            <div class="col-12 col-md-4 text-center ">
                <img src="{{ asset('img/users/' . $user->profile_img_name) }}" alt="Foto de perfil"
                    class="img-fluid rounded mb-4 w-100 border-0">

                <a href="{{ route('users.edit', $user->user_id) }}" class="btn btn-outline-warning w-100 mb-2">
                    <i class="bi bi-pencil-square"></i> Editar
                </a>

                {{-- Botón para ir al CRUD de "Mis piezas" --}}
                <a href="{{ route('users.user_products.index', ['userId' => $user->user_id]) }}"
                   class="btn btn-outline-info w-100">
                    <i class="bi bi-cpu"></i> Administrar mis piezas
                </a>
            </div>

            {{-- Columna derecha --}}
            <div class="col-12 col-md-8 text-white fs-5"> {{-- fs-5 ≈ 20 px --}}

                <p class="mb-3">
                    <span class="fw-semibold">N.º de cliente:</span>
                    <input type="text" class="form-control border-0" value="{{ $user->user_id }}" disabled>
                </p>

                <p class="mb-3">
                    <span class="fw-semibold">Nombre(s):</span>
                    <input type="text" class="form-control border-0" value="{{ $user->name }}" disabled>
                </p>

                <p class="mb-3">
                    <span class="fw-semibold">Apellido(s):</span>
                    <input type="text" class="form-control border-0" value="{{ $user->last_name }}" disabled>
                </p>

                <p class="mb-3">
                    <span class="fw-semibold">Correo:</span>
                    <input type="text" class="form-control border-0" value="{{ $user->email }}" disabled>
                </p>

                <p class="mb-3">
                    <span class="fw-semibold">Teléfono:</span>
                    <input type="text" class="form-control border-0" value="{{ $user->phone_number }}" disabled>
                </p>

                <p class="mb-0">
                    <span class="fw-semibold">Dirección:</span>
                    <input type="text" class="form-control border-0" value="{{ $user->address }}" disabled>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ===================== Sección: Piezas subidas por el usuario ===================== --}}
<div class="card p-4 container bg-accent1 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-white mb-0">Mis piezas publicadas</h2>

        {{-- Mismo botón para ir al CRUD completo --}}
        <a href="{{ route('users.user_products.index', ['userId' => $user->user_id]) }}"
           class="btn btn-sm btn-outline-light">
            Ver todas / administrar
        </a>
    </div>

    @if(isset($userProducts) && $userProducts->isNotEmpty())
        <div class="table-responsive">
            <table class="table mb-0 text-center align-middle">
                <thead>
                    <tr>
                        <th class="text-white">ID</th>
                        <th class="text-white">Nombre</th>
                        <th class="text-white">Precio</th>
                        <th class="text-white">Stock</th>
                        <th class="text-white">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($userProducts as $item)
                        <tr>
                            <td class="text-white">{{ $item->user_product_id }}</td>
                            <td class="text-white">{{ $item->name }}</td>
                            <td class="text-white">${{ number_format($item->price, 2) }}</td>
                            <td class="text-white">{{ $item->stock }}</td>
                            <td class="text-white">{{ $item->condition ?? 'Sin especificar' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="text-white-50 mt-2 mb-0">
            Mostrando tus últimas {{ $userProducts->count() }} piezas publicadas.
        </p>
    @else
        <div class="alert alert-secondary mb-0">
            <strong>Aún no has publicado ninguna pieza.</strong><br>
            Empieza a vender tus componentes dando clic en
            <a href="{{ route('users.user_products.index', ['userId' => $user->user_id]) }}" class="alert-link">
                "Administrar mis piezas"
            </a>.
        </div>
    @endif
    {{-- ===================== Sección: Reportes recibidos ===================== --}}

    <br><br>
<div class="card p-4 container bg-accent1 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-white mb-0">
            Reportes recibidos sobre mis piezas
        </h2>
    </div>

    @if(isset($reports) && $reports->isNotEmpty())
        <div class="table-responsive">
            <table class="table mb-0 text-center align-middle">
                <thead>
                    <tr>
                        <th class="text-white">#</th>
                        <th class="text-white">Pieza</th>
                        <th class="text-white">Reportado por</th>
                        <th class="text-white">Motivo</th>
                        <th class="text-white">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td class="text-white">{{ $report->report_id }}</td>
                            <td class="text-white">
                                {{ $report->userProduct->name ?? 'Eliminada' }}
                            </td>
                            <td class="text-white">
                                {{ $report->reporter->username ?? 'Usuario desconocido' }}
                            </td>
                            <td class="text-white text-start">
                                {{ $report->reason }}
                            </td>
                            <td class="text-white-50">
                                {{ $report->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-success mb-0">
            <strong>No has recibido ningún reporte.</strong> Tus piezas están en buena reputación 😊
        </div>
    @endif
</div>
</div>

@endsection
