@extends("layout.PlantillaUser")
@section('content')

{{-- No se ayuda lol --}}

<div class="container-fluid bg-primary justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="container py-5">
        <!-- Un solo div que contiene todo lo solicitado -->
        <div class="row g-4 align-items-center text-white rounded p-4 mt-5 bg-accent1">

            <!-- Imagen (1/3) -->
            <div class="col-12 col-md-4">
                <img src="{{ asset('img/Logo_pagina.png') }}" class="img-fluid rounded w-100">
            </div>

            <!-- Párrafo principal (2/3) -->
            <div class="col-12 col-md-8">
                <h3>Nuestra misión</h3>
                <p class="fs-5 mb-0 text-justify">
                    <strong>Nexus Tech</strong> es una compañía dedicada a la venta de piezas de computadora que coloca
                    al
                    consumidor en el centro de cada decisión. Nos apasiona ofrecer componentes de alto rendimiento,
                    asesoría honesta y un servicio post-venta de primera. Nuestro compromiso es que cada cliente
                    encuentre la solución perfecta para su configuración, sin complicaciones y al mejor precio.
                </p>
            </div>

            <!-- Segundo párrafo debajo de toda la franja anterior -->
            <div class="col-12">
                <p class="mt-3">
                    Ya sea que busques actualizar tu tarjeta gráfica para obtener más FPS, ampliar tu almacenamiento
                    con un SSD de última generación o armar un equipo desde cero, en Nexus Tech encontrarás un
                    catálogo cuidadosamente curado, envíos rápidos y soporte técnico real — porque tu experiencia
                    como usuario es nuestra prioridad absoluta.
                </p>
            </div>

            <!-- NUEVA SECCIÓN: 3 IMÁGENES EN COLUMNAS -->
            <div class="col-12 m-auto">
                <div class="row text-center ">
                    <div class=" col-md-4 mb-3">
                        <img src="{{asset('/img/aboutus/1.jpg')}}" class="img-fluid rounded h-100" alt="Imagen 1">
                    </div>
                    <div class="col mb-3">
                        <img src="{{asset('/img/aboutus/2.png')}}" class="img-fluid rounded h-100" alt="Imagen 2">
                    </div>
                    <div class="col mb-3">
                        <img src="{{asset('/img/aboutus/3.jpg')}}" class="img-fluid rounded h-100" alt="Imagen 3">
                    </div>
                </div>
            </div>
            <!-- FIN DE NUEVA SECCIÓN -->

        </div>
    </div>
</div>

@endsection