@extends('layout.PlantillaAdmin')
@section('content')

{{-- No se ayuda lqxd --}}

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<div class="container-fluid bg-primary  flex-column justify-content-center align-items-center"
    style="min-height: 100vh; font-family: roboto">
    <br>
    <br>
    <br>
    <div class="container card bg-accent1 p-5">

        <div class="container">
            <h1>Listada de usuarios registrados</h1><br>

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <div class="">
                <table class="table mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead class="text-center">
                        <tr>
                            <th class="bg-dark border border-primary" style="border-top-left-radius:1.2rem">ID</th>
                            <th class="bg-dark border border-primary">Nombre(s)</th>
                            <th class="bg-dark border border-primary">Apellido(s)</th>
                            <th class="bg-dark border border-primary">Correo</th>
                            <th class="bg-dark border border-primary">Rol</th>
                            <th class="bg-dark border border-primary" style="border-top-right-radius:1.2rem">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($users as $user)

                        <tr>
                            <td class="bg-secondary border border-primary">{{ $user->user_id }}</td>
                            <td class="bg-secondary border border-primary">{{ $user->name }}</td>
                            <td class="bg-secondary border border-primary">{{ $user->last_name }}</td>
                            <td class="bg-secondary border border-primary">{{ $user->email }}</td>
                            <td class="bg-secondary border border-primary">{{ $user->role->name ?? 'N/A' }}
                            </td>

                           <td class="bg-secondary border border-primary">
                            {{-- Ver perfil rápido (modal) --}}
                            <button type="button" class="btn btn-outline-info mb-1"
                                data-bs-toggle="modal"
                                data-bs-target="#ShowProduct{{ $user->user_id }}">
                                <i class="bi bi-card-list"></i> Ver perfil
                            </button>
                            |

                            {{-- Ver piezas publicadas por este usuario --}}
                            <a href="{{ route('admin.user_products.index', $user) }}"
                                class="btn btn-outline-warning mb-1">
                                <i class="bi bi-cpu"></i> Ver piezas
                            </a>

                            |

                            {{-- Banear usuario --}}
                            <button type="button" class="btn btn-outline-danger mb-1"
                                data-bs-toggle="modal"
                                data-bs-target="#DeleteProduct{{ $user->user_id }}">
                                <i class="bi bi-trash-fill"></i> Banear
                            </button>
                        </td>

                        </tr>

                        {{-- Modal SHOW (dentro del bucle) --}}
                        <div class="modal fade border-0" id="ShowProduct{{ $user->user_id }}" tabindex="-1"
                            aria-labelledby="modalLabel{{ $user->user_id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg border-0">
                                <div class="modal-content bg-accent1 border-0 p-3">
                                    <div class="modal-header border-0">
                                        <h3 class="modal-title" id="modalLabel{{ $user->user_id }}">Perfil de Usuario
                                        </h3>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body border-0">
                                        <div class="card bg-secondary border-0">
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- Columna de imagen (1/3) -->
                                                    <div class="col-md-4 text-center mb-3">
                                                        <img src="{{ asset('img/users/' . $user->profile_img_name) }}"
                                                            class="img-fluid rounded border border-secondary"
                                                            style="max-height: 200px;" alt="Imagen de perfil">
                                                    </div>

                                                    <!-- Columna de información (2/3) -->
                                                    <div class="col-md-8">
                                                        <h5 class="card-title">{{ $user->username }}</h5>
                                                        <p class="mb-3">
                                                            <span class="fw-semibold">N.º de cliente:</span>
                                                            <input type="text" class="form-control border-0"
                                                                value="{{ $user->user_id }}" disabled>
                                                        </p>

                                                        <p class="mb-3">
                                                            <span class="fw-semibold">Nombre(s):</span>
                                                            <input type="text" class="form-control border-0"
                                                                value="{{ $user->name }}" disabled>
                                                        </p>

                                                        <p class="mb-3">
                                                            <span class="fw-semibold">Apellido(s):</span>
                                                            <input type="text" class="form-control border-0"
                                                                value="{{ $user->last_name }}" disabled>
                                                        </p>

                                                        <p class="mb-3">
                                                            <span class="fw-semibold">Correo:</span>
                                                            <input type="text" class="form-control border-0"
                                                                value="{{ $user->email }}" disabled>
                                                        </p>

                                                        <p class="mb-3">
                                                            <span class="fw-semibold">Teléfono:</span>
                                                            <input type="text" class="form-control border-0"
                                                                value="{{ $user->phone_number }}" disabled>
                                                        </p>

                                                        <p class="mb-0">
                                                            <span class="fw-semibold">Dirección:</span>
                                                            <input type="text" class="form-control border-0"
                                                                value="{{ $user->address }}" disabled>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pb-4">
                                        <button type="button" class="btn btn-lg btn-primary rounded"
                                            data-bs-dismiss="modal">Regresar</button>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Delete modal -->
                        <div class="modal fade" id="DeleteProduct{{ $user->user_id }}" tabindex="-1"
                            aria-labelledby="deleteModalLabel{{ $user->user_id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content bg-accent1 border-0 p-3">
                                    <div class="modal-header border-0">
                                        <h3 class="modal-title" id="deleteModalLabel{{ $user->user_id }}">¿Estás seguro
                                            que quieres banear este usuario?</h3>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>
                                    </div>


                                        <div class="modal-body">
                                            <div class="card bg-secondary border-0">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <!-- Imagen (1/3) -->
                                                        <div class="col-md-4 text-center mb-3">
                                                            <img src="{{ asset('img/users/' . $user->profile_img_name) }}"
                                                                class="img-fluid rounded border border-secondary"
                                                                style="max-height: 200px;" alt="Imagen de perfil">
                                                        </div>

                                                        <!-- Información (2/3) -->
                                                        <div class="col-md-8">
                                                            <h5 class="card-title">{{ $user->username }}</h5>
                                                            <p class="mb-3">
                                                                <span class="fw-semibold">N.º de cliente:</span>
                                                                <input type="text" class="form-control border-0"
                                                                    value="{{ $user->user_id }}" disabled>
                                                            </p>

                                                            <p class="mb-3">
                                                                <span class="fw-semibold">Nombre(s):</span>
                                                                <input type="text" class="form-control border-0"
                                                                    value="{{ $user->name }}" disabled>
                                                            </p>

                                                            <p class="mb-3">
                                                                <span class="fw-semibold">Apellido(s):</span>
                                                                <input type="text" class="form-control border-0"
                                                                    value="{{ $user->last_name }}" disabled>
                                                            </p>

                                                            <p class="mb-3">
                                                                <span class="fw-semibold">Correo:</span>
                                                                <input type="text" class="form-control border-0"
                                                                    value="{{ $user->email }}" disabled>
                                                            </p>

                                                            <p class="mb-3">
                                                                <span class="fw-semibold">Teléfono:</span>
                                                                <input type="text" class="form-control border-0"
                                                                    value="{{ $user->phone_number }}" disabled>
                                                            </p>

                                                            <p class="mb-0">
                                                                <span class="fw-semibold">Dirección:</span>
                                                                <input type="text" class="form-control border-0"
                                                                    value="{{ $user->address }}" disabled>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="modal-footer border-0">
                                            <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-lg btn-outline-danger border-3 rounded">
                                                    <i class="bi bi-trash-fill"></i> <b>Banear</b> </button>
                                                <button type="button" class="btn btn-lg btn-primary rounded"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                            </form>
                                        </div>

                                </div>
                            </div>
                        </div>


                        @endforeach
                        <tr class="text-center bg-dark">
                            <td class="border-primary" style="border-bottom-left-radius:1.3rem"></td>
                            <td class="border-primary"></td>
                            <td class="border-primary"></td>
                            <td class="border-primary"></td>
                            <td class="border-primary"></td>
                            <td class="border-primary" style="border-bottom-right-radius:1.3rem"></td>
                        </tr>
                    </tbody>
                </table>
            </div><br>
            {{ $users->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>

@endsection
