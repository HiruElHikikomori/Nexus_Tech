<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * POST /reports
     * Reportar una pieza de usuario
     */
    public function store(StoreReportRequest $request)
    {
        $data   = $request->validated();
        $userId = Auth::id();

        // 1) Evitar duplicado (mismo user + misma pieza)
        $already = Report::where('user_id', $userId)
            ->where('user_product_id', $data['user_product_id'])
            ->exists();

        if ($already) {
            return $this->responseBackOrJson(
                $request,
                ['report' => 'Ya enviaste un reporte para esta pieza.'],
                null,
                422
            );
        }

        // 2) Obtener el dueño de la pieza reportada
        $ownerId = DB::table('user_products')
            ->where('user_product_id', $data['user_product_id'])
            ->value('user_id');

        if (!$ownerId) {
            return $this->responseBackOrJson(
                $request,
                ['not_found' => 'La pieza no existe o fue eliminada.'],
                null,
                404
            );
        }

        // 3) Evitar que el usuario reporte su propia pieza
        if ((int)$ownerId === (int)$userId) {
            return $this->responseBackOrJson(
                $request,
                ['auth' => 'No puedes reportar tu propia pieza.'],
                null,
                403
            );
        }

        // 4) Crear el reporte
        $report = Report::create([
            'user_id'         => $userId,             // quien reporta
            'reported_user_id'=> $ownerId,            // dueño de la pieza
            'user_product_id' => $data['user_product_id'],
            'reason'          => $data['reason'],
            'status'          => 'pending',
        ]);

        // 5) Incrementar contador de reportes en user_products (opcional)
        DB::table('user_products')
            ->where('user_product_id', $data['user_product_id'])
            ->increment('report_count');

        return $this->responseBackOrJson(
            $request,
            [],
            'Reporte enviado. ¡Gracias por avisar!'
        );
    }

    /**
     * Respuesta consistente para JSON o redirección
     */
    private function responseBackOrJson(Request $request, array $errors = [], ?string $successMsg = null, int $status = 200)
    {
        if ($request->expectsJson()) {
            if ($errors) {
                return response()->json(['success' => false, 'errors' => $errors], $status);
            }
            return response()->json(['success' => true, 'message' => $successMsg], $status);
        }

        if ($errors) {
            return back()->withErrors($errors)->withInput();
        }

        return back()->with('success', $successMsg);
    }
}
