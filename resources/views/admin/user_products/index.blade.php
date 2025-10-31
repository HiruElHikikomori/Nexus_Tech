@extends('layout.PlantillaAdmin')

@section('content')
<div class="container-fluid bg-primary py-4" style="min-height:100vh;">
  <div class="container bg-accent1 card p-3">
    <h4 class="mb-3">Piezas de usuarios</h4>

    <div class="table-responsive">
      <table class="table align-middle table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Precio</th>
            <th>Vendedor</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse(($items ?? []) as $it)
            <tr>
              <td>#{{ $it->user_product_id }}</td>
              <td><img src="{{ asset('img/user_products/' . ($it->img_name ?? 'default.png')) }}" style="width:56px;height:56px;object-fit:cover" class="rounded"></td>
              <td>{{ $it->name }}</td>
              <td>{{ $it->type->name ?? '—' }}</td>
              <td>$ {{ number_format($it->price ?? 0, 2) }}</td>
              <td>{{ $it->owner->name ?? '—' }}</td>
              <td class="text-end">
                <form method="POST" action="{{ route('admin.user_products.destroy', $it->user_product_id) }}" onsubmit="return confirm('¿Eliminar la pieza #{{ $it->user_product_id }}?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">Sin piezas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center">
      {{ ($items ?? null)?->links() }}
    </div>
  </div>
</div>
@endsection
