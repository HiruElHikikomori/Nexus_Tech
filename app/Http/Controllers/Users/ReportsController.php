<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    // POST /reports
    public function store(StoreReportRequest $request)
    {
        $data = $request->validated();

        Report::create([
            'user_id'         => Auth::id(),
            'product_id'      => $data['product_id'] ?? null,
            'user_product_id' => $data['user_product_id'] ?? null,
            'reason'          => $data['reason'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Reporte enviado. ¡Gracias por avisar!']);
        }
        return back()->with('success', 'Reporte enviado. ¡Gracias por avisar!');
    }
}
