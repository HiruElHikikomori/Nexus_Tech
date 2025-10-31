<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    // Listado con filtros rápidos
    public function index(Request $request)
    {
        $q = Report::query()
            ->with(['reporter:id,name', 'product:id,name', 'userProduct:id,name', 'reportedUser:id,name'])
            ->latest();

        if ($request->filled('status')) {
            $q->where('status', $request->string('status')); // 'pending' | 'resolved'
        }

        if ($request->filled('type')) {
            // type: product | user_product | user
            $type = $request->string('type');
            if ($type === 'product')      $q->whereNotNull('product_id');
            if ($type === 'user_product') $q->whereNotNull('user_product_id');
            if ($type === 'user')         $q->whereNotNull('reported_user_id');
        }

        if ($search = $request->string('search')->toString()) {
            $q->where(function ($w) use ($search) {
                $w->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('userProduct', fn($up) => $up->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('reportedUser', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('reporter', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $reports = $q->paginate(15)->withQueryString();

        return view('admin.reports.index', compact('reports'));
    }

    // Detalle simple
    public function show(Report $report)
    {
        $report->load(['reporter', 'product', 'userProduct', 'reportedUser']);
        return view('admin.reports.show', compact('report'));
    }

    // Marcar como resuelto
    public function resolve(Report $report, Request $request)
    {
        $report->status = 'resolved';
        $report->resolved_by = auth()->id();
        $report->resolved_at = now();
        $report->save();

        return back()->with('success', 'Reporte marcado como resuelto.');
    }

    // Eliminar (si ya no es necesario conservar evidencia)
    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('admin.reports.index')->with('success', 'Reporte eliminado.');
    }

    // Acciones masivas
    public function bulkResolve(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        if (!$ids) return back()->withErrors('No se seleccionaron reportes.');

        DB::table('reports')
            ->whereIn('report_id', $ids)   // ajusta a tu PK real
            ->update([
                'status' => 'resolved',
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Reportes marcados como resueltos.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        if (!$ids) return back()->withErrors('No se seleccionaron reportes.');

        DB::table('reports')->whereIn('report_id', $ids)->delete(); // ajusta PK
        return back()->with('success', 'Reportes eliminados.');
    }
}
