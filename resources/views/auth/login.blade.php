@extends("layout.PlantillaUser")
@section('content')

<div class="container-fluid d-flex justify-content-center align-items-center">
    <div class="card bg-accent1 p-4 shadow-lg w-25 position-absolute top-50 start-50 translate-middle">
        <h2 class="text-center mb-4">Login</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="mb-3">
                <input type="text" name="username" class="form-control border-0" placeholder="Usuario" value="{{ old('username') }}">
            </div>

            <div class="mb-3">
                <input type="password" name="password" class="form-control  border-0" placeholder="Contraseña">
            </div>

            <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
        </form>

        <p class="mt-3 mb-0 text-white">
            ¿No tienes una cuenta? <a href="{{ url('/register') }}" class="text-decoration-none text-info">Regístrate</a>
        </p>
    </div>
</div>

@endsection

