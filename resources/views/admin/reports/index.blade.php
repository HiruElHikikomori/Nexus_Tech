@extends('layout.PlantillaAdmin')

@section('content')
<div class="container-fluid bg-primary py-4" style="min-height:100vh;">
  <div class="container bg-accent1 card p-3">
    <h4 class="mb-3">Reportes</h4>

    <form method="GET" class="row g-2 mb-3">
      <div class="col-md-3">
        <select name="status" class="form-select">
          <option value="">Todos los estados</option>
          <option value="pending"  {{ request('status')==='pending'?'selected':'' }}>Pendientes</option>
          <option value="resolved" {{ request('status')==='resolved'?'selected':'' }}>Resueltos</option>
        </select>
      </div>
      <div class="col-md-3">
        <select name="type" class="form-select">
          <option value="">Todos los tipos</option>
          <option value="product"      {{ request('type')==='product'?'selected':'' }}>Producto</option>
          <option value="user_product" {{ request('type')==='user_product'?'selected':'' }}>Pieza de usuario</option>
          <option value="user"         {{ request('type')==='user'?'selected':'' }}>Usuario</option>
        </select>
      </div>
      <div class="col-md-4">
        <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar por razón…">
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-outline-light">Filtrar</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table align-middle table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Razón</th>
            <th>Reportado por</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse(($reports ?? []) as $r)
            @php
              $tipo = $r->product_id ? 'Producto' : ($r->user_product_id ? 'Pieza usuario' : 'Usuario');
            @endphp
            <tr>
              <td>#{{ $r->report_id }}</td>
              <td>{{ $tipo }}</td>
              <td class="text-truncate" style="max-width: 320px;">{{ $r->reason }}</td>
              <td>{{ $r->reporter->name ?? '—' }}</td>
              <td>
                <span class="badge {{ ($r->status ?? 'pending') === 'resolved' ? 'bg-success' : 'bg-warning text-dark' }}">
                  {{ ucfirst($r->status ?? 'pending') }}
                </span>
              </td>
              <td>{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
              <td class="text-end">
                <a href="{{ route('admin.reports.show', $r->report_id) }}" class="btn btn-sm btn-outline-info">Ver</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">Sin reportes.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center">
      {{ ($reports ?? null)?->links() }}
    </div>
  </div>
</div>
@endsection
