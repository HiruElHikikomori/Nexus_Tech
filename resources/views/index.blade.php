@extends("layout.PlantillaUser")
@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12 col-lg-15 mx-auto"
         style="height:33vh; overflow:hidden;">
      
      <div id="carouselExampleCaptions"
           class="carousel slide h-100 "
           data-bs-ride="carousel">
        
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExampleCaptions"
                  data-bs-slide-to="0" class="active"
                  aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#carouselExampleCaptions"
                  data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#carouselExampleCaptions"
                  data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <div class="carousel-inner h-100 rounded ">
          <div class="carousel-item active h-100">
            <img src="{{asset('/img/carusel/nvidia gpu.png')}}"
                 class="d-block w-100 h-100 object-fit-cover">
          </div>
          <div class="carousel-item h-100">
            <img src="{{asset('/img/carusel/amd gpu2.jpg')}}"
                 class="d-block w-100 h-100 object-fit-cover">
          </div>
          <div class="carousel-item h-100">
            <img src="{{asset('/img/carusel/amd server.jpg')}}"
                 class="d-block w-100 h-100 object-fit-cover">
          </div>
        </div>

                    <button class="carousel-control-prev" type="button"
                            data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button"
                            data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Lista aleatoria de productos --}}
        <div class="row bg-accent1 rounded p-5 my-5 m-auto">
            <h1 class="">Sugerencias</h1>
            @foreach($randProducts as $product)
            <div class="card m-auto my-3 p-0 bg-secondary text-accent3 border-0" style="width: 18rem;">
                <img src="{{ asset('img/products/' . $product->img_name) }}" class="card-img-top" style="height:17rem">
                <div class="card-body text-center">
                    <form class="add-to-cart-form" action="{{ route('cart.add-item') }}" method="POST">
                        <input type="hidden" name="product_id" value="{{ $product->products_id }}">
                        <input type="hidden" name="count" value="1">
                        <h5 class="card-title">{{$product->name}}</h5>
                        <p class="card-text">$ {{ number_format($product->price, 2) }}</p>
                        
                        <button type="button" class="btn btn-lg rounded btn-outline-info " data-bs-toggle="modal" data-bs-target="#ShowProduct{{ $product->products_id }}"><i class="bi bi-card-list"></i></button>

                        @csrf
                        <button type="submit" class="btn btn-lg rounded btn-outline-success offset-md-4"><i class="bi bi-plus"></i></button>
                    </form>

                </div>
            </div>

            {{-- Modal SHOW (dentro del bucle) --}}
            <div class="modal fade" id="ShowProduct{{ $product->products_id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content p-3 bg-accent1 text-white border border-accent1">
                        <div class="modal-header border border border-accent1">
                            <h3 class="modal-title" id="exampleModalLabel">Detalles de la pieza</h3>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body border-0">
                            <div class="card bg-secondary border-0 p-3">
                                <div class="card-body row container-fluid border-0">

                                    <div class="col">
                                        <img class="img-fluid rounded border-0" src="{{ asset('img/products/' . $product->img_name) }}" style="height: 12rem;">
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
                            <form class="add-to-cart-form" action="{{ route('cart.add-item') }}" method="POST">
                                <input type="hidden" name="product_id" value="{{ $product->products_id }}">
                                <input type="hidden" name="count" value="1">
                                @csrf
                                <button type="submit" class="btn btn-lg rounded-pill btn-outline-success"><i class="bi bi-plus"></i>Añadir al carrito</button>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>

            @endforeach
    </div>



</div>
    
@endsection

@push('scripts')
<script>
    function showToast(message, type = 'info') {
        const toastElement = document.getElementById('liveToast');
        if (!toastElement) {
            console.error('Toast element not found. Please ensure #liveToast exists in your HTML.');
            alert(message);
            return;
        }
        const toast = new bootstrap.Toast(toastElement);
        const toastTitle = document.getElementById('toast-title');
        const toastBody = document.getElementById('toast-body');

        if (toastTitle) toastTitle.textContent = type.charAt(0).toUpperCase() + type.slice(1);
        if (toastBody) toastBody.textContent = message;

        toastElement.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
        switch (type) {
            case 'success':
                toastElement.classList.add('bg-success');
                break;
            case 'danger':
                toastElement.classList.add('bg-danger');
                break;
            case 'warning':
                toastElement.classList.add('bg-warning');
                break;
            case 'info':
            default:
                toastElement.classList.add('bg-info');
                break;
        }
        toast.show();
    }

    document.addEventListener('DOMContentLoaded', function() {

        // Lógica existente para el carrito (mantener sin cambios)
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');

                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                axios.post(this.action, formData)
                    .then(response => {
                        if (response.data.success) {
                            showToast(response.data.message, 'success');
                        } else {
                            showToast('Error: ' + response.data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error al añadir al carrito:', error);
                        let errorMessage = 'Hubo un error al procesar tu solicitud.';
                        if (error.response && error.response.data && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        }
                        showToast(errorMessage, 'danger');
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="bi bi-plus"></i>';
                    });
            });
        });
    });
</script>
@endpush