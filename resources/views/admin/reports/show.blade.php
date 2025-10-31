@extends('layout.PlantillaAdmin')

@section('content')
<div class="container-fluid bg-primary py-4" style="min-height:100vh;">
  <div class="container bg-accent1 card p-3">
    <h4>Reporte #{{ $report->report_id }}</h4>

    <div class="mb-2">
      <b>Estado:</b>
      <span class="badge {{ ($report->status ?? 'pending') === 'resolved' ? 'bg-success' : 'bg-warning text-dark' }}">
        {{ ucfirst($report->status ?? 'pending') }}
      </span>
    </div>

    @if($report->product)
      <div class="mb-1"><b>Producto:</b> {{ $report->product->name }} (ID {{ $report->product->products_id }})</div>
    @endif
    @if($report->userProduct)
      <div class="mb-1"><b>Pieza de usuario:</b> {{ $report->userProduct->name }} (ID {{ $report->userProduct->user_product_id }})</div>
    @endif
    @if($report->reportedUser)
      <div class="mb-1"><b>Usuario reportado:</b> {{ $report->reportedUser->name }} (ID {{ $report->reportedUser->user_id }})</div>
    @endif

    <div class="mb-3"><b>Reportado por:</b> {{ $report->reporter->name ?? '—' }}</div>
    <div class="mb-3"><b>Razón:</b> <div class="mt-1">{{ $report->reason }}</div></div>

    <div class="d-flex gap-2">
      @if(($report->status ?? 'pending') !== 'resolved')
      <form method="POST" action="{{ route('admin.reports.resolve', $report->report_id) }}">
        @csrf
        <button class="btn btn-outline-success">Marcar como resuelto</button>
      </form>
      @endif

      <form method="POST" action="{{ route('admin.reports.destroy', $report->report_id) }}" onsubmit="return confirm('¿Eliminar este reporte?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">Eliminar</button>
      </form>

      <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>
  </div>
</div>
@endsection
