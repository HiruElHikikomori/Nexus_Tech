@extends('layout.PlantillaUser')

@section('content')
    {{-- Añade un data-product-url aquí para pasar la URL de la página de productos a JS --}}
    <div class="container" id="cart-container" data-product-url="{{ route('user.product') }}">

        <div class="p-5 py-2 pt-5 m-5 bg-accent1 rounded m-auto" id="cart-items-wrapper">
            {{-- ID para el contenedor de los items --}}

            <h1>Carrito de compras</h1>

            @php
                $subtotalGeneral = 0;
            @endphp

            @forelse($CartItems as $Item)
                @php
                    // Puede venir de products (oficial) o de user_products (pieza de usuario)
                    $product = $Item->product ?? $Item->userProduct;

                    // Carpeta de imágenes según el tipo de producto
                    $imageFolder = $Item->user_product_id ? 'img/user_products/' : 'img/products/';

                    // Nombre de imagen (usa una por defecto si no hay)
                    $imgName = $product?->img_name ?? 'default.png';
                @endphp

                @if(!$product)
                    {{-- Si no hay producto asociado (por ejemplo, se borró), mostramos algo decente --}}
                    <div class="align-middle bg-primary p-5 my-5 row rounded cart-item-row"
                         data-item-id="{{ $Item->id_cart_items }}">
                        <div class="col align-self-center">
                            <img class="img-fluid align-middle rounded"
                                 src="{{ asset('img/products/default.png') }}"
                                 style="width: 15rem"
                                 alt="Producto no disponible">
                        </div>
                        <div class="col align-self-center">
                            <h4>Producto no disponible</h4>
                            <h4>$ <span class="unit-price">{{ number_format($Item->unit_price, 2) }}</span></h4>
                        </div>
                        <div class="col align-self-center">
                            <form class="delete-item-form"
                                  data-item-id="{{ $Item->id_cart_items }}"
                                  action="{{ route('cart.destroy', $Item->id_cart_items) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-lg btn-outline-danger border-4 bg-dark w-50">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @continue
                @endif

                <div class="align-middle bg-primary p-5 my-5 row rounded cart-item-row"
                     data-item-id="{{ $Item->id_cart_items }}">
                    {{-- Imagen del producto --}}
                    <div class="col align-self-center">
                        <img class="img-fluid align-middle rounded"
                             src="{{ asset($imageFolder . $imgName) }}"
                             style="width: 15rem"
                             alt="{{ $product?->name ?? 'Producto no encontrado' }}">
                    </div>

                    {{-- Datos del producto --}}
                    <div class="col align-self-center">
                        <h4>{{ $product?->name ?? 'producto no encontrado' }}</h4>
                        <h4>$ <span class="unit-price">{{ number_format($Item->unit_price, 2) }}</span></h4>
                    </div>

                    {{-- Botón eliminar --}}
                    <div class="col align-self-center">
                        <form class="delete-item-form"
                              data-item-id="{{ $Item->id_cart_items }}"
                              action="{{ route('cart.destroy', $Item->id_cart_items) }}"
                              method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-lg btn-outline-danger border-4 bg-dark w-50">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </div>

                    {{-- Cantidad + / - --}}
                    <div class="col align-self-center">
                        <div class="d-grid gap-2 d-md-block">
                            {{-- Disminuir --}}
                            <form class="update-quantity-form"
                                  action="{{ route('cart.update', $Item->id_cart_items) }}"
                                  method="POST"
                                  style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="operation" value="decrease">
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger border-4 bg-dark rounded-circle"
                                        title="Disminuir cantidad">
                                    <i class="bi bi-dash h1"></i>
                                </button>
                            </form>

                            <h1 class="btn btn-link link-underline link-underline-opacity-0 btn-lg text-white disabled item-count">
                                {{ $Item->count }}
                            </h1>

                            {{-- Aumentar --}}
                            <form class="update-quantity-form"
                                  action="{{ route('cart.update', $Item->id_cart_items) }}"
                                  method="POST"
                                  style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="operation" value="increase">
                                <button type="submit"
                                        class="btn btn-sm btn-outline-success border-4 bg-dark rounded-circle"
                                        title="Aumentar cantidad">
                                    <i class="bi bi-plus h1"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Subtotal --}}
                    <div class="align-self-center text-end">
                        @php
                            $subtotalItem = $Item->count * $Item->unit_price;
                            $subtotalGeneral += $subtotalItem;
                        @endphp
                        <h5>Subtotal: $
                            <span class="item-subtotal">{{ number_format($subtotalItem, 2) }}</span>
                        </h5>
                    </div>
                </div>
            @empty
                <div id="empty-cart-message" class="text-center p-5">
                    <p class="lead">Tu carrito está vacío. ¡Añade algunos productos!</p>
                    {{-- El href será actualizado por JavaScript al cargar la página o al vaciarse el carrito --}}
                    <a href="#" class="btn btn-primary" id="go-to-products-btn">Ver Productos</a>
                </div>
            @endforelse

            {{-- Resumen del carrito (se oculta si está vacío) --}}
            <div id="cart-summary" @if($CartItems->isEmpty()) style="display:none;" @endif>
                <h4>
                    El pedido se enviará a la siguiente dirección:
                    <strong>{{ Auth::user()->address }}</strong>
                </h4>
                <p>
                    Total de productos:
                    <span id="total-products-count">{{ $CartItems->sum('count') }}</span>
                </p>
                <h3>
                    Total a pagar: $
                    <span id="total-to-pay">{{ number_format($subtotalGeneral, 2) }}</span>
                </h3>
            </div>

            {{-- Botones de navegación y compra --}}
            @if($CartItems->isNotEmpty())
                <div class="row m-auto my-5">
                    <a href="{{ url('/products') }}"
                       class="col col-md-2 btn btn-lg btn-primary d-flex justify-content-start rounded">
                        Regresar al listado
                    </a>

                    {{-- Este formulario dispara el CHECKOUT (crea order + order_items y vacía el carrito) --}}
                    <form id="clear-cart-form"
                          class="col d-flex justify-content-end"
                          action="{{ route('cart.checkout') }}"
                          method="POST">
                        @csrf
                        <button type="submit" class="btn btn-lg btn-outline-success rounded">
                            Realizar compra
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
@endsection
