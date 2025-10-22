@extends('layout.PlantillaAdmin')
@section('content')

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<div class="container-fluid flex-column justify-content-center align-items-center mb-5"
    style="min-height: 100vh;">
    <br>
    <br>
    <div class="container bg-accent1 card p-5">
        <h1 class="mb-4">Listado de Productos</h1>

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        <div class="text-start mb-3">
            <button type="button" class="btn btn-lg btn-outline-success border-3 rounded" data-bs-toggle="modal"
                data-bs-target="#CreateProduct">
                <b>Crear Nuevo Producto</b>
            </button>
        </div>

        <div class="">
            <table class="table card-table mb-0" style="border-collapse: separate; border-spacing: 0;">
                <thead class="text-center">
                    <tr class="">
                        <th class="bg-dark border border-primary" style="border-top-left-radius:1.2rem">ID</th>
                        <th class="bg-dark border border-primary">Nombre</th>
                        <th class="bg-dark border border-primary">Tipo de Producto</th>
                        <th class="bg-dark border border-primary">Precio</th>
                        <th class="bg-dark border border-primary">Stock</th>
                        <th class="bg-dark border border-primary" style="border-top-right-radius:1.2rem">Acciones</th>
                    </tr>
                </thead>
                @if($products->isEmpty())

                    <h2>Lo que estás buscando no existe</h2>
                @endif
                <tbody class="">
                    @foreach ($products as $product)
                    
                    <tr class="text-center">
                        <td class="bg-secondary border border-primary">{{ $product->products_id }}</td>
                        <td class="bg-secondary border border-primary">{{ $product->name }}</td>
                        <td class="bg-secondary border border-primary">
                            {{ $product->product_type->name ?? 'N/A' }}</td>
                        <td class="bg-secondary border border-primary">
                            ${{ number_format($product->price, 2) }}</td>
                        <td class="bg-secondary border border-primary">{{ $product->stock }}</td>
                        <td class="bg-secondary border border-primary">
                            <button type="button" class="btn btn-outline-info btn-sm rounded" data-bs-toggle="modal"
                                data-bs-target="#ShowProduct{{ $product->products_id }}">
                                <i class="bi bi-card-list"></i> Ver
                            </button> |
                            <button type="button" class="btn btn-outline-warning btn-sm rounded" data-bs-toggle="modal"
                                data-bs-target="#EditProduct{{ $product->products_id }}">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button> |
                            <button type="button" class="btn btn-outline-danger btn-sm rounded" data-bs-toggle="modal"
                                data-bs-target="#DeleteProduct{{ $product->products_id }}">
                                <i class="bi bi-trash-fill"></i> Eliminar
                            </button>
                        </td>
                    </tr>


                    {{-- Modal SHOW (dentro del bucle) --}}
                    <div class="modal fade" id="ShowProduct{{ $product->products_id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content p-3 bg-accent1 border-0">
                                <div class="modal-header border border border-accent1">
                                    <h3 class="modal-title" id="exampleModalLabel">Detalles de la pieza</h3>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body border-0">
                                    <div class="card bg-secondary border-0 p-3">
                                        <div class="card-body row container-fluid">

                                            <div class="col">
                                                <img class="img-fluid rounded border-0" src="{{ asset('img/products/' . $product->img_name) }}" style="height: auto;">
                                            </div>
                                            <div class="col-8">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">No. de producto:</label>
                                                    <input type="text" class="form-control border-0" id="name" name="name" value="{{ $product->products_id }}" disabled required>
                                                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nombre:</label>
                                                    <input type="text" class="form-control border-0" id="name" name="name" value="{{ $product->name }}" disabled required>
                                                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Tipo:</label>
                                                    <input type="text" class="form-control border-0" id="name" name="name" value="{{ $product->product_type->name ?? 'N/A' }}" disabled required>
                                                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Descripción:</label>
                                                    <textarea class="form-control border-0" id="exampleFormControlTextarea1" rows="3" disabled>{{ $product->description }}</textarea>
                                                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Precio:</label>
                                                    <input type="text" class="form-control border-0" id="name" name="name" value="$ {{ number_format($product->price, 2) }}" disabled required>
                                                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Stock:</label>
                                                    <input type="text" class="form-control border-0" id="name" name="name" value="{{ $product->stock }}" disabled required>
                                                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border border-accent1">
                                    <button type="button" class="btn btn-lg btn-primary rounded-pill" data-bs-dismiss="modal">Regresar</button>                                 
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit modal -->
                    <div class="modal fade" id="EditProduct{{ $product->products_id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content bg-secondary p-3 border-0">
                                <div class="modal-header border-0">
                                    <h3 class="modal-title" id="exampleModalLabel">Editar producto</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <form action="{{ route('admin.products.update', $product->products_id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body bg-accent1 rounded border-0 p-4">
                                        <div class="row ">
                                            <!-- Columna izquierda: Imagen -->
                                            <div class="col-md-4 mb-3 border-0">
                                                
                                                <img src="{{ asset('img/products/' . $product->img_name) }}"
                                                    class="img-fluid rounded mb-2" style="max-width: 100%;">
                                                <input type="file" class="form-control border-0" id="img_name" name="img_name">
                                                <small class="form-text text-muted">Deja en blanco para mantener la
                                                    imagen actual.</small>
                                                @error('img_name') <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Columna derecha: Formulario -->
                                            <div class="col-md-8 border-0">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nombre del Producto:</label>
                                                    <input type="text" class="form-control border-0" id="name" name="name"
                                                        value="{{ $product->name }}" required>
                                                    @error('name') <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="product_type_id" class="form-label">Tipo de
                                                        Producto:</label>
                                                    <select class="form-select border-0" id="product_type_id"
                                                        name="product_type_id" required>
                                                        <option value="">Selecciona un tipo</option>
                                                        @foreach ($productTypes as $type)
                                                        <option value="{{ $type->product_type_id }}"
                                                            {{ ($product->product_type_id == $type->product_type_id) ? 'selected' : '' }}>
                                                            {{ $type->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('product_type_id') <div class="text-danger">{{ $message }}
                                                    </div> @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Descripción:</label>
                                                    <textarea class="form-control border-0" id="description" name="description"
                                                        rows="5" required>{{ $product->description }}</textarea>
                                                    @error('description') <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="price" class="form-label ">Precio:</label>
                                                    <input type="number" step="0.01" class="form-control border-0" id="price"
                                                        name="price" value="{{ $product->price }}" required>
                                                    @error('price') <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="stock" class="form-label">Stock:</label>
                                                    <input type="number" class="form-control border-0" id="stock" name="stock"
                                                        value="{{ $product->stock }}" required>
                                                    @error('stock') <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-0">
                                        <button type="submit" class="btn btn-lg btn-outline-warning border-3 rounded">
                                            <i class="bi bi-pencil-square"></i> Actualizar Producto
                                        </button>

                                        <button type="button" class="btn btn-lg btn-primary rounded"
                                            data-bs-dismiss="modal">Cancelar</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
        </div>

        <!-- Delete modal -->
        <div class="modal fade" id="DeleteProduct{{ $product->products_id }}" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-accent1 border-0 p-3">
                    <div class="modal-header border-0">
                        <h3 class="modal-title" id="exampleModalLabel">¿Estás seguro que quieres eliminar este producto?
                        </h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.products.destroy', $product->products_id) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body">
                            <div class="card bg-secondary border-0 p-4">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Columna izquierda: Imagen -->
                                        <div class="col-md-4 mb-3">
                                            <img src="{{ asset('img/products/' . $product->img_name) }}"
                                                class="img-fluid rounded" style="max-width: 100%;">
                                        </div>

                                        <!-- Columna derecha: Detalles -->
                                        <div class="col-8">
                                            <div class="mb-3 text-white">
                                                <label for="name text-white" class="form-label">No. de producto:</label>
                                                <input type="text" class="form-control border-0" id="name" name="name"
                                                    value="{{ $product->products_id }}" disabled required>
                                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="mb-3 text-white">
                                                <label for="name" class="form-label">Nombre:</label>
                                                <input type="text" class="form-control border-0" id="name" name="name"
                                                    value="{{ $product->name }}" disabled required>
                                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="mb-3 text-white">
                                                <label for="name" class="form-label">Tipo:</label>
                                                <input type="text" class="form-control border-0" id="name" name="name"
                                                    value="{{ $product->product_type->name ?? 'N/A' }}" disabled
                                                    required>
                                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="mb-3 text-white text-white">
                                                <label for="name" class="form-label">Descripción:</label>
                                                <textarea class="form-control border-0" id="exampleFormControlTextarea1"
                                                    rows="3" disabled>{{ $product->description }}</textarea>
                                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="mb-3 text-white">
                                                <label for="name" class="form-label">Precio:</label>
                                                <input type="text" class="form-control border-0" id="name" name="name"
                                                    value="$ {{ number_format($product->price, 2) }}" disabled required>
                                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="mb-3 text-white">
                                                <label for="name" class="form-label">Stock:</label>
                                                <input type="text" class="form-control border-0" id="name" name="name"
                                                    value="{{ $product->stock }}" disabled required>
                                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0">
                            <button type="submit" class="btn btn-lg btn-outline-danger border-3 rounded" data-bs-dismiss="modal"><i
                                    class="bi bi-trash-fill"></i><b> Eliminar</b></button>
                            <button type="button" class="btn btn-lg btn-primary rounded" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>


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

{{ $products->links('vendor.pagination.bootstrap-5') }}
</div>
</div>
</div>

@endsection

<!-- Modal Create -->
<div class="modal fade" id="CreateProduct" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-secondary border-0 p-3">
            <div class="modal-header border-0">
                <h3 class="modal-title" id="exampleModalLabel">Crear nuevo producto</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="row bg-accent1 rounded p-5">
                        <div class="mb-3">
                            <label for="img_name" class="form-label">Nombre del producto:</label>
                            <input type="text" class="form-control border-0" placeholder="Nombre" id="name" name="name" value="{{ old('name') }}"
                            required>
                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col mb-3">
                            <label for="img_name" class="form-label">Tipo de producto:</label>
                            <select class="form-select border-0" id="product_type_id" aria-label="Default select example" name="product_type_id" required>
                                <option value="">Selecciona un tipo</option>
                                @foreach ($productTypes as $type)
                                <option value="{{ $type->product_type_id }}"
                                    {{ old('product_type_id') == $type->product_type_id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('product_type_id') <div class="text-danger">{{ $message }}</div> @enderror

                            
                        </div>

                        <div class="col mb-3">
                            <label for="img_name" class="form-label">Precio para el producto:</label>
                            <input type="number" step="0.01" class="form-control border-0" placeholder="Precio" id="price" name="price"
                                value="{{ old('price') }}" required>
                            @error('price') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="img_name" class="form-label">Descripción breve:</label>
                            <textarea class="form-control border-0" id="description" placeholder="Descripción" name="description" rows="5"
                                required>{{ old('description') }}</textarea>
                            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>


                        <div class="col mb-3">
                            <label for="img_name" class="form-label">Stock:</label>
                            <input type="number" class="form-control border-0" placeholder="Cantidad" id="stock" name="stock" value="{{ old('stock') }}"
                                required>
                            @error('stock') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col mb-3">
                            <label for="img_name" class="form-label">Imagen del producto:</label>
                            <input type="file" class="form-control border-0" id="img_name" name="img_name"
                                value="{{ old('img_name') }}">
                            @error('img_name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                </div>
                    

                <div class="modal-footer border-0 m-auto">
                    <button type="submit" class="btn btn-lg btn-outline-success border-3 rounded">  <b>Guardar Producto</b>
                        </button>
                    <button type="button" class="btn btn-lg btn-primary rounded" data-bs-dismiss="modal">Cancelar</button>

                </div>
            </form>
        </div>
    </div>
</div>