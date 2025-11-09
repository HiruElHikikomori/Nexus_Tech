@extends('layout.PlantillaAdmin')

@section('content')
<div class="container-fluid bg-primary d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="text-center text-white mt-3">
        <h1>¡Bienvenido {{ Auth::user()->username }}!</h1>
    </div>

    <div class="mt-4 px-4 p-5">
        <div class="bg-accent1 d-flex flex-column align-items-center p-5 rounded-5 shadow-lg">
            
            <!-- Fila de botones -->
            <div class="d-flex justify-content-between gap-5 flex-wrap text-center">
                
                <!-- Botón Productos -->
                <a href="{{ route('admin.products.index') }}" class="text-decoration-none flex-fill card-hover">
                    <div class="bg-secondary p-5 rounded-top d-flex justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#64ca92" class="bi bi-box2 w-75" viewBox="0 0 16 16">
                            <path d="M2.95.4a1 1 0 0 1 .8-.4h8.5a1 1 0 0 1 .8.4l2.85 3.8a.5.5 0 0 1 .1.3V15a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4.5a.5.5 0 0 1 .1-.3zM7.5 1H3.75L1.5 4h6zm1 0v3h6l-2.25-3zM15 5H1v10h14z"/>
                        </svg>
                    </div>
                    <div class="bg-dark text-white text-center p-5 rounded-bottom">
                        <h1 class="mt-auto">Productos</h1>
                    </div>
                </a>

                <!-- Botón Usuarios -->
                <a href="{{ route('admin.users.index') }}" class="text-decoration-none flex-fill card-hover">
                    <div class="bg-secondary p-5 rounded-top d-flex justify-content-center align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#64ca92" class="bi bi-person w-75" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                        </svg>
                    </div>
                    <div class="bg-dark text-white text-center p-5 rounded-bottom">
                        <h1 class="text-white mt-auto">Usuarios</h1>
                    </div>
                </a>

            </div>
        </div>
    </div>
</div>

{{-- Estilos visuales --}}
<style>
    .card-hover {
        transition: all 0.3s ease-in-out;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 0 0 transparent;
    }

    .card-hover:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
    }

    .card-hover:hover .bg-secondary {
        background-color: #479168ff !important;
        transition: background-color 0.3s ease;
    }

    .card-hover:hover svg {
        fill: #fff !important;
        transform: scale(1.05);
        transition: all 0.3s ease;
    }

    .card-hover:hover .bg-dark {
        background-color: #0b2e26 !important;
    }

    .card-hover h1 {
        transition: color 0.3s ease;
    }

    .card-hover:hover h1 {
        color: #64ca92 !important;
    }
</style>
@endsection
