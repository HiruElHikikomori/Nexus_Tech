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
     */
    public function store(StoreReportRequest $request)
    {
        $data   = $request->validated();
        $userId = Auth::id();

        // 1) Evitar duplicado (mismo user + mismo item)
        $already = Report::where('user_id', $userId)
            ->when(!empty($data['product_id']), fn($q) => $q->where('product_id', $data['product_id']))
            ->when(!empty($data['user_product_id']), fn($q) => $q->where('user_product_id', $data['user_product_id']))
            ->exists();

        if ($already) {
            return $this->responseBackOrJson(
                $request,
                ['report' => 'Ya enviaste un reporte para este artículo.'],
                null,
                422
            );
        }

        // 2) (Opcional) No permitir reportar tu propio user_product
        if (!empty($data['user_product_id'])) {
            $ownerId = DB::table('user_products')
                ->where('user_product_id', $data['user_product_id'])
                ->value('user_id');

            if ($ownerId && (int)$ownerId === (int)$userId) {
                return $this->responseBackOrJson(
                    $request,
                    ['auth' => 'No puedes reportar tu propio producto.'],
                    null,
                    403
                );
            }
        }

        // 3) Crear reporte
        $report = Report::create([
            'user_id'         => $userId,
            'product_id'      => $data['product_id'] ?? null,
            'user_product_id' => $data['user_product_id'] ?? null,
            'reason'          => $data['reason'],
        ]);

        // 4) (Opcional) Incrementar contador de reportes en user_products
        if (!empty($data['user_product_id'])) {
            DB::table('user_products')
                ->where('user_product_id', $data['user_product_id'])
                ->increment('report_count');
        }

        return $this->responseBackOrJson(
            $request,
            [],
            'Reporte enviado. ¡Gracias por avisar!'
        );
    }

    /**
     * Respuesta consistente: JSON (422/403/200) o back() con errores/success.
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
