<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>nexus_tech</title>
</head>

<body class="d-flex flex-column min-vh-100">
    
    @stack('scripts')
    <!-- 🔷 NAVBAR: barra superior de navegación -->
    <nav class=" text-white py-3 navbar navbar-expand-lg" style= "background-color: #111B1F;">
        <div class="container-fluid">

            <!-- Logo del sitio -->
            <a class="navbar-brand" href="#">
                 <img src="{{ asset('img/Logo_pagina.png') }}" style="max-height: 100px;" alt="Logo">
            </a>

            <!-- Menú, búsqueda y carrito -->
            <div class="d-flex me-5 align-items-center">

                <!-- 🔹 Menú de navegación -->
                <ul class="navbar-nav flex-row me-3 gap-5">
                    <li class="nav-item me-2">
                        <a href="#" class="nav-link link-info px-2">Inicio</a>
                    </li>
                    <li class="nav-item me-2">
                        <a href="{{ url('/products') }}" class="nav-link link-info px-2">Catálogo</a>
                    </li>
                    <li class="nav-item me-2">
                        <a href="#" class="nav-link link-info px-2">Sobre nosotros</a>
                    </li>
                    @guest
                    <li class="nav-item me-2">
                        <a href="{{ url('/login') }}" class="nav-link link-info px-2">Iniciar session</a>
                    </li>
                    <li class="nav-item me-2">
                        <a href="{{ url('/register') }}" class="nav-link link-info px-2">Registrarse</a>
                    </li>
                    @endguest
                    
 
                </ul>

                <!-- 🔹 Barra de búsqueda -->
                <form class="d-flex align-items-center me-3" role="search" style="position: relative; max-width: 500px;">
                    <input class="form-control" type="search" placeholder="Buscar" aria-label="Search" style="padding-right: 100px;">
                    <button class="btn btn-outline-success position-absolute" type="submit"
                            style="top: 0; right: 0; height: 100%; border-top-left-radius: 0; border-bottom-left-radius: 0;">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                <!-- 🔹 Botón del carrito -->
                <li class="d-flex align-items-center nav-item">
                    <a class="nav-link" href="{{ route('user.cart') }}">
                        <i class="bi bi-cart"></i>
                        <span class="badge bg-danger rounded-pill" id="cart-count">{{$newCartItemCount ?? 0}}</span>
                    </a>
                </li>

            </div>
        </div>
    </nav>

    <!-- 🔻 Contenido dinámico (Blade) -->
    @yield('content')

    <!-- 🔸 FOOTER: pie de página fijo -->
    <footer class="footer d-flex text-light  mt-auto" style="background-color: #111B1F;">
        <div class="container-fluid py-3 mx-5">
            <div class="row align-items-center text-center text-md-start justify-content-between">
                
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
                <div class="col-md-2 mb-2 mb-md-0 text-center">
                    <a class="navbar-brand" href="#">
                        <img src="{{ asset('img/Logo_pagina.png') }}" class="img-fluid" style="max-height: 100px;" alt="Logo">
                    </a>
                </div>

                <!-- 🔹 Columna 3: Información de contacto y redes sociales -->
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="row">
                        <!-- Contacto -->
                        <div class="col-md-6">
                            <p class="mb-1 fw-bold">Contáctanos</p>
                            <p class="mb-0"><i class="bi bi-envelope"></i> contacto@nexus.com</p>
                            <p class="mb-0"><i class="bi bi-telephone"></i> +52 656 123 4567</p>
                        </div>

                        <!-- Redes sociales -->
                        <div class="col-md text-md-end">
                            <p class="mb-1">Nuestras redes sociales</p>
                            <a href="https://facebook.com" target="_blank" class="btn btn-outline-light btn-sm me-2">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://instagram.com" target="_blank" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-instagram"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </footer>

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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Script de carrito --}}
    <script src="{{ asset('js/cart.js') }}"></script>
</body>


</html>