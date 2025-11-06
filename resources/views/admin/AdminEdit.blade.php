@extends('layout.PlantillaAdmin') {{-- Asume que tienes un layout principal llamado app.blade.php --}}
{{-- No se ayuda qq --}}
@section('content')

<div class="container bg-accent1 card p-5 text-white">
    <h1>Editar Perfil de: {{ $user->username }}</h1>

    @if (session('success'))
    <div class="alert alert-success text-white"> {{-- texto blanco --}}
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger text-white"> {{-- texto blanco --}}
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card-body">
        {{-- Formulario de edición de perfil --}}
        <form action="{{ route('admin.update', $user->user_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                {{-- Columna izquierda: imagen + botón --}}
                <div class="col-12 col-md-4 text-center">
                    @if ($user->profile_img_name)
                    <img src="{{ $user->profile_img_url }}" alt="Imagen de perfil actual"
                        class="img-fluid rounded mb-3">
                    @endif

                    <div class="mb-3 text-start">
                        <label for="profile_img" class="form-label">Imagen de Perfil:</label>
                        <input type="file" class="form-control border-0" id="profile_img" name="profile_img">
                        <small class="form-text text-white-50">
                            Deja en blanco para mantener la imagen actual.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-outline-warning w-100">
                        <i class="bi bi-pencil-square"></i>
                        Actualizar Perfil
                    </button>
                </div>

                {{-- Columna derecha: resto del formulario --}}
                <div class="col-12 col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre:</label>
                        <input type="text" class="form-control border-0" id="name" name="name"
                            value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">Apellido:</label>
                        <input type="text" class="form-control border-0" id="last_name" name="last_name"
                            value="{{ old('last_name', $user->last_name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Nombre de Usuario:</label>
                        <input type="text" class="form-control border-0" id="username" name="username"
                            value="{{ old('username', $user->username) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico:</label>
                        <input type="email" class="form-control border-0" id="email" name="email"
                            value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Número de Teléfono:</label>
                        <input type="text" class="form-control border-0" id="phone_number" name="phone_number"
                            value="{{ old('phone_number', $user->phone_number) }}">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Dirección:</label>
                        <textarea class="form-control border-0" id="address" name="address"
                            rows="2">{{ old('address', $user->address) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label border-0">Nueva Contraseña:</label>
                        <input type="password" class="form-control border-0" id="password" name="password">
                        <small class="form-text text-white-50">
                            Deja en blanco para mantener la contraseña actual.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña:</label>
                        <input type="password" class="form-control border-0" id="password_confirmation"
                            name="password_confirmation">
                    </div>
                </div>
            </div>
        </form>

        <div class="text-end">
            <a href="{{ url('/adminProfile') }}" class="btn btn-secondary">Regresar</a>
        </div>

    </div>
</div>
@endsection