@extends("layout.PlantillaUser")
@section('content')

<div class="container-fluid d-flex justify-content-center align-items-center">
    <div class="card p-4 bg-accent1 w-25">
        <h2 class="text-center mb-4">Registro</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" enctype="multipart/form-data" action="{{ url('/register') }}">
            @csrf
            <div class="row">
                <div class="col mb-3">
                    <input type="text" name="name" class="form-control border-0" placeholder="Nombre/s" value="{{ old('name') }}">
                </div>

                <div class="col mb-3">
                    <input type="text" name="last_name" class="form-control border-0" placeholder="Apellido/s" value="{{ old('last_name') }}">
                </div>
            </div>
            

            <div class="mb-3">
                <input type="text" name="username" class="form-control border-0" placeholder="Usuario" value="{{ old('username') }}">
            </div>

            <div class="row">
                <div class="col mb-3">
                    <input type="email" name="email" class="form-control border-0" placeholder="Correo electrónico" value="{{ old('email') }}">
                </div>
                <div class="col mb-3">
                    <input type="text" name="phone_number" class="form-control border-0" placeholder="Teléfono" value="{{ old('phone_number') }}">
                </div>
            </div>
            

            <div class="mb-3">
                <input type="password" name="password" class="form-control border-0" placeholder="Contraseña">
            </div>

            <div class="mb-3">
                <input type="password" name="password_confirmation" class="form-control border-0" placeholder="Confirmar contraseña">
            </div>

            

            <div class="col mb-3">
                    <input type="text" name="address" class="form-control border-0" placeholder="Dirección" value="{{ old('address') }}">
                </div>

            <div class="mb-3">
                <input type="file" name="profile_img_name" class="form-control border-0">
            </div>

            <button type="submit" class="btn btn-primary w-100">Registrarse</button>
        </form>

        <p class="mt-3 text-center mb-0 text-white">
            ¿Ya tienes una cuenta? <a href="{{ url('/login') }}" class="text-decoration-none text-info">Inicia sesión</a>
        </p>
    </div>
</div>

@endsection
