<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" rel="stylesheet">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>nexus_tech</title>
</head>

@stack('scripts')


<body class="d-flex flex-column bg-primary min-vh-100 " data-bs-theme="dark" style="font-family:montserrat, sans-serif">

    <!-- 🔷 NAVBAR: barra superior de navegación -->
    <nav class="bg-secondary d-flex py-3 navbar navbar-expand-lg">
        <div class="container-fluid">

            <!-- Logo del sitio -->
            <a class="navbar-brand" href="{{ url('/') }} ">
                <img src="{{ asset('img/Logo_pagina.png') }}" class="img-fluid" style="max-height: 100px;" alt="Logo">
            </a>

            <!-- Menú, búsqueda y carrito -->
            <div class="d-flex row">

                <!-- 🔹 Menú de navegación -->
                <ul class="col navbar-nav flex-row me-5 gap-5 input-group">
                    <li class="nav-item">
                        <a href="{{ url('/') }}" class="nav-link link-info px-2">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/products') }}" class="nav-link link-info px-2">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/aboutus') }}" class="nav-link link-info px-2">Sobre nosotros</a>
                    </li>
                    @guest
                    <li class="nav-item">
                        <a href="{{ url('/login') }}" class="nav-link link-info px-2">Iniciar sesión</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/register') }}" class="nav-link link-info px-2">Registrarse</a>
                    </li>
                    @endguest
                    @auth
                    <li class="nav-item">
                        <a href="{{ url('/userProfile') }}" class="nav-link link-info px-2">{{ Auth::user()->username }}</a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button type="submit" class="nav-link link-danger px-2">Cerrar sesión</button>
                        </form>

                    </li>
                    @endauth

                    <!-- 🔹 Barra de búsqueda -->
                    <li class="nav-item rounded border-0 input-group-text">
                        <form class="d-flex align-items-center" role="search" action="{{ route('products.search') }}" method="GET">
                            <input class="form-control rounded border-0 w-100" type="search" placeholder="Buscar" aria-label="Search" name="query" value="{{ request('query') }}">

                            <button class="btn" type="submit">
                                <i class="bi bi-search"></i>
                            </button>

                        </form>
                    </li>


                    <!-- 🔹 Botón del carrito -->
                    <li class="nav-item d-flex align-items-center">
                        <a class="btn btn-lg rounded border-0" href="{{ route('cart.index') }}">
                            <i class="bi bi-cart"></i>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <main class="py-5 flex-grow-1" style="font-family:roboto">
        <!-- 🔻 Contenido dinámico (Blade) -->
        @yield('content')

    </main>





    {{-- Contenedor para las notificaciones Toast (normalmente en layout.index) --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto" id="toast-title">Notificación</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toast-body">
                </div>
        </div>
    </div>

    <!-- 🔸 FOOTER: pie de página fijo -->
    <footer class="footer d-flex text-light mt-auto bg-secondary">
        <div class="container-fluid py-3 mx-5">
            <div class="row align-items-center text-center text-md-start justify-content-between m-auto">

                <!-- 🔹 Columna 1: Texto informativo de la empresa -->
                <div class="col-md-4 mb-2 mb-md-0">
                    <small>
                        <p class="mb-1 fw-bold">✨En NexusTech, ¡te ofrecemos los mejores proveedores! ✨</p>
                        <ul class="list-unstyled mb-0">
                            <li>Componentes y piezas de equipos de cómputo.</li>
                            <li>Productos tecnológicos de calidad.</li>
                        </ul>
                    </small>
                </div>

                <!-- 🔹 Columna 2: Logo centrado -->
                <div class="col-md-2">
                    <a class="navbar-brand" href="#">
                        <img src="{{ asset('img/Logo_pagina.png') }}" class="img-fluid mh-100 px-5">
                    </a>
                </div>

                <!-- 🔹 Columna 3: Información de contacto y redes sociales -->
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="row">
                        <!-- Contacto -->
                        <div class="col-md-6">
                            <p class="mb-1 fw-bold">Contáctanos</p>
                            <p class="mb-0"><i class="bi bi-envelope"></i> nexustech443@gmail.com</p>
                            <p class="mb-0"><i class="bi bi-telephone"></i> +52 656-822-7384</p>
                        </div>

                        <!-- Redes sociales -->
                        <div class="col-md text-md-end">
                            <p class="mb-1">Nuestras redes sociales</p>
                            <a href="https://www.facebook.com/profile.php?id=61578618472687" target="_blank" class="btn btn-outline-light btn-sm me-2">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://www.instagram.com/nexus_tech443/" target="_blank" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-instagram"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Script de carrito --}}
    <script src="{{ asset('js/cart.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
</body>




</html>
